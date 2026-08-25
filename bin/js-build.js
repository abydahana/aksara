const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const rootDir = path.join(__dirname, '..');

const targetDirs = [
    path.join(rootDir, 'aksara', 'Modules'),
    path.join(rootDir, 'modules'),
    path.join(rootDir, 'themes'),
    path.join(rootDir, 'public', 'assets', 'local')
];

// Helper to recursively find files in directory by extension
function findFiles(dir, ext, excludeMin = true, fileList = []) {
    if (!fs.existsSync(dir)) return fileList;
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);
        if (stat.isDirectory()) {
            findFiles(filePath, ext, excludeMin, fileList);
        } else if (file.endsWith(ext)) {
            if (excludeMin && file.endsWith(`.min${ext}`)) {
                continue;
            }
            fileList.push(filePath);
        }
    }
    return fileList;
}

const args = process.argv.slice(2);
const isJsOnly = args.includes('--js');
const isCssOnly = args.includes('--css');

const doJs = isJsOnly || (!isCssOnly);
const doCss = isCssOnly || (!isJsOnly);

// -------------------------------------------------------------
// STEP 1 & 2: Gather JS and CSS source files
// -------------------------------------------------------------
let jsFiles = [];
let cssFiles = [];

for (const dir of targetDirs) {
    if (doJs) findFiles(dir, '.js', true, jsFiles);
    if (doCss) findFiles(dir, '.css', true, cssFiles);
}

// -------------------------------------------------------------
// STEP 1: Format JS & CSS source files with Prettier
// -------------------------------------------------------------
if (doJs) {
    console.log('\n1. Formatting JavaScript source files with Prettier...');
    if (jsFiles.length > 0) {
        try {
            const fileArgs = jsFiles.map((f) => `"${f}"`).join(' ');
            execSync(`npx -y prettier --write ${fileArgs}`, { stdio: 'inherit' });
        } catch (e) {
            console.error(' Prettier JS formatting failed:', e.message);
        }
    } else {
        console.log(' No JavaScript source files found.');
    }
}

if (doCss) {
    console.log('\n2. Formatting CSS source files with Prettier...');
    if (cssFiles.length > 0) {
        try {
            const fileArgs = cssFiles.map((f) => `"${f}"`).join(' ');
            execSync(`npx -y prettier --write ${fileArgs}`, { stdio: 'inherit' });
        } catch (e) {
            console.error(' Prettier CSS formatting failed:', e.message);
        }
    } else {
        console.log(' No CSS source files found.');
    }
}

function cleanTemplateInner(inner) {
    if (!inner.includes('\n') && !inner.includes('\r')) return inner;
    return inner
        .replace(/^\r?\n\s*/, '')
        .replace(/\r?\n\s*$/, '')
        .replace(/>\r?\n\s*</g, '><')
        .replace(/>\r?\n\s*/g, '>')
        .replace(/\r?\n\s*</g, '<')
        .replace(/\r?\n\s*/g, ' ');
}

function minifyTemplateLiterals(code) {
    const regex = /`([^`\\]*(?:\\.[^`\\]*)*)`/g;
    let prev;
    let count = 0;
    do {
        prev = code;
        code = code.replace(regex, (match, inner) => '`' + cleanTemplateInner(inner) + '`');
        count++;
    } while (code !== prev && count < 10);
    return code;
}

// -------------------------------------------------------------
// STEP 2: Minify JS & CSS source files to .min.js and .min.css
// -------------------------------------------------------------
let jsMinifiedCount = 0;
if (doJs) {
    console.log('\n3. Minifying JavaScript source files to .min.js...');
    for (const srcFile of jsFiles) {
        const minFile = srcFile.replace(/\.js$/, '.min.js');
        const tempFile = path.join(__dirname, `_temp_${path.basename(srcFile)}`);
        try {
            let code = fs.readFileSync(srcFile, 'utf8');
            code = minifyTemplateLiterals(code);

            fs.writeFileSync(tempFile, code, 'utf8');
            execSync(`npx -y terser "${tempFile}" -o "${minFile}" --compress --mangle`, { stdio: 'pipe' });
            console.log(` Minified: ${path.relative(rootDir, srcFile)} -> ${path.relative(rootDir, minFile)}`);
            jsMinifiedCount++;
        } catch (e) {
            console.error(` Failed to minify ${srcFile}:`, e.message);
        } finally {
            if (fs.existsSync(tempFile)) {
                fs.unlinkSync(tempFile);
            }
        }
    }
}

let cssMinifiedCount = 0;
if (doCss) {
    console.log('\n4. Minifying CSS source files to .min.css...');
    for (const srcFile of cssFiles) {
        const minFile = srcFile.replace(/\.css$/, '.min.css');
        try {
            execSync(`npx -y csso-cli "${srcFile}" -o "${minFile}"`, { stdio: 'pipe' });
            console.log(` Minified: ${path.relative(rootDir, srcFile)} -> ${path.relative(rootDir, minFile)}`);
            cssMinifiedCount++;
        } catch (e) {
            console.error(` Failed to minify ${srcFile}:`, e.message);
        }
    }
}

console.log(`\n Total formatted & minified: ${jsMinifiedCount} JS files, ${cssMinifiedCount} CSS files.`);

// -------------------------------------------------------------
// STEP 3: Scan PHP/View files for unminified JS/CSS calls and throw error
// -------------------------------------------------------------
console.log('\n5. Scanning PHP and View files for unminified JS/CSS references...');

function scanUnminifiedReferences() {
    const scanDirs = [
        path.join(rootDir, 'aksara'),
        path.join(rootDir, 'modules'),
        path.join(rootDir, 'themes'),
        path.join(rootDir, 'public')
    ];

    const phpViewFiles = [];
    for (const d of scanDirs) {
        findFiles(d, '.php', false, phpViewFiles);
    }

    // Build a list of known local project source files
    const knownSourceAssets = [...jsFiles, ...cssFiles].map((f) => ({
        fullPath: f,
        relativePath: path.relative(rootDir, f).replace(/\\/g, '/').toLowerCase(),
        basename: path.basename(f).toLowerCase(),
        minPath: f.replace(/\.(js|css)$/, '.min.$1')
    }));

    const unminifiedList = [];
    const assetRegex = /['"]([^'"]+?\.(js|css))['"]/g;

    for (const file of phpViewFiles) {
        const relativeFile = path.relative(rootDir, file).replace(/\\/g, '/');
        const content = fs.readFileSync(file, 'utf8');
        const lines = content.split('\n');

        lines.forEach((line, index) => {
            let match;
            while ((match = assetRegex.exec(line)) !== null) {
                const rawAssetPath = match[1];

                // Ignore external URLs (contains ://, starts with // or data:)
                if (rawAssetPath.includes('://') || rawAssetPath.startsWith('//') || rawAssetPath.startsWith('data:')) {
                    continue;
                }
                // Ignore already minified references
                if (rawAssetPath.endsWith('.min.js') || rawAssetPath.endsWith('.min.css')) {
                    continue;
                }

                const cleanAssetPath = rawAssetPath.replace(/^\/+/, '').toLowerCase();
                const assetBasename = path.basename(cleanAssetPath);

                // Match against known project source files
                const matchedAsset = knownSourceAssets.find((item) => {
                    if (item.basename !== assetBasename) return false;

                    const itemRelWithoutAksara = item.relativePath.replace(/^aksara\//, '');
                    return (
                        item.relativePath.endsWith(cleanAssetPath) ||
                        itemRelWithoutAksara.endsWith(cleanAssetPath) ||
                        cleanAssetPath.endsWith(item.basename)
                    );
                });

                if (matchedAsset && fs.existsSync(matchedAsset.minPath)) {
                    const minAssetPath = rawAssetPath.replace(/\.(js|css)$/, '.min.$1');
                    unminifiedList.push({
                        file: relativeFile,
                        line: index + 1,
                        asset: rawAssetPath,
                        minAsset: minAssetPath
                    });
                }
            }
        });
    }

    if (unminifiedList.length > 0) {
        console.error('\n ❌ ERROR: Found unminified JS/CSS asset references!');
        console.error(' Please update the following references to use the minified (.min) versions:\n');
        unminifiedList.forEach((item) => {
            console.error(`   - ${item.file}:${item.line} -> "${item.asset}" (Use "${item.minAsset}")`);
        });
        console.error('\nBuild failed due to unminified asset references.');
        process.exit(1);
    } else {
        console.log(' All JS/CSS asset references are properly minified.');
    }
}

scanUnminifiedReferences();
console.log('\nCompleted successfully!');

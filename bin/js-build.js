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

// -------------------------------------------------------------
// STEP 1 & 2: Gather JS and CSS source files
// -------------------------------------------------------------
let jsFiles = [];
let cssFiles = [];

for (const dir of targetDirs) {
    findFiles(dir, '.js', true, jsFiles);
    findFiles(dir, '.css', true, cssFiles);
}

// -------------------------------------------------------------
// STEP 1: Format JS & CSS source files with Prettier
// -------------------------------------------------------------
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

function cleanStringPart(str) {
    if (!str.includes('\n') && !str.includes('\r')) return str;
    return str
        .replace(/^\r?\n\s*/, '')
        .replace(/\r?\n\s*$/, '')
        .replace(/>\r?\n\s*</g, '><')
        .replace(/>\r?\n\s*/g, '>')
        .replace(/\r?\n\s*</g, '<')
        .replace(/\r?\n\s*/g, ' ');
}

function minifyTemplateLiterals(code) {
    let result = '';
    let i = 0;
    const n = code.length;
    const stack = [];

    let inString = null;
    let inComment = null;
    let currentPart = '';

    while (i < n) {
        const char = code[i];
        const nextChar = code[i + 1];

        if (!inString && (!stack.length || stack[stack.length - 1].inExpr)) {
            if (!inComment && char === '/' && nextChar === '/') {
                inComment = '//';
            } else if (inComment === '//' && (char === '\n' || char === '\r')) {
                inComment = null;
            } else if (!inComment && char === '/' && nextChar === '*') {
                inComment = '/*';
            } else if (inComment === '/*' && char === '*' && nextChar === '/') {
                inComment = null;
                result += '*/';
                i += 2;
                continue;
            }
        }

        if (inComment) {
            result += char;
            i++;
            continue;
        }

        const top = stack.length ? stack[stack.length - 1] : null;

        if (top && top.type === 'TEMPLATE' && !top.inExpr) {
            if (char === '\\') {
                currentPart += char + (nextChar || '');
                i += 2;
                continue;
            }
            if (char === '`') {
                result += cleanStringPart(currentPart) + '`';
                currentPart = '';
                stack.pop();
                i++;
                continue;
            }
            if (char === '$' && nextChar === '{') {
                result += cleanStringPart(currentPart) + '${';
                currentPart = '';
                top.inExpr = true;
                top.braceDepth = 1;
                i += 2;
                continue;
            }
            currentPart += char;
            i++;
            continue;
        }

        if (inString) {
            if (char === '\\') {
                currentPart += char + (nextChar || '');
                i += 2;
                continue;
            }
            if (char === inString) {
                inString = null;
            }
            result += char;
            i++;
            continue;
        }

        if (char === "'" || char === '"') {
            inString = char;
            result += char;
            i++;
            continue;
        }

        if (char === '`') {
            stack.push({ type: 'TEMPLATE', inExpr: false, braceDepth: 0 });
            currentPart = '';
            result += '`';
            i++;
            continue;
        }

        if (top && top.type === 'TEMPLATE' && top.inExpr) {
            if (char === '{') {
                top.braceDepth++;
            } else if (char === '}') {
                top.braceDepth--;
                if (top.braceDepth === 0) {
                    top.inExpr = false;
                    currentPart = '';
                    result += '}';
                    i++;
                    continue;
                }
            }
        }

        result += char;
        i++;
    }

    return result;
}

// -------------------------------------------------------------
// STEP 2: Minify JS & CSS source files to .min.js and .min.css
// -------------------------------------------------------------
console.log('\n3. Minifying JavaScript source files to .min.js...');
let jsMinifiedCount = 0;

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

console.log('\n4. Minifying CSS source files to .min.css...');
let cssMinifiedCount = 0;

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

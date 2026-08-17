const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Find and format JS source files
function findJsFiles(dir, fileList = []) {
    if (!fs.existsSync(dir)) return fileList;
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);
        if (stat.isDirectory()) {
            findJsFiles(filePath, fileList);
        } else if (file.endsWith('.js') && !file.endsWith('.min.js')) {
            fileList.push(filePath);
        }
    }
    return fileList;
}

const targetDirs = [
    path.join(__dirname, '..', 'aksara', 'Modules'),
    path.join(__dirname, '..', 'modules'),
    path.join(__dirname, '..', 'themes'),
    path.join(__dirname, '..', 'public', 'assets', 'local')
];

let srcFiles = [];
for (const dir of targetDirs) {
    findJsFiles(dir, srcFiles);
}

console.log('\n2. Formatting JavaScript source files with Prettier...');
if (srcFiles.length > 0) {
    try {
        const fileArgs = srcFiles.map((f) => `"${f}"`).join(' ');
        execSync(`npx -y prettier --write ${fileArgs}`, { stdio: 'inherit' });
    } catch (e) {
        console.error('Prettier JS formatting failed:', e.message);
    }
}

// Minify JS source files to .min.js
console.log('\n3. Minifying JavaScript source files to .min.js...');

let minifiedCount = 0;

for (const srcFile of srcFiles) {
    const minFile = srcFile.replace(/\.js$/, '.min.js');
    const tempFile = path.join(__dirname, `_temp_${path.basename(srcFile)}`);
    try {
        let code = fs.readFileSync(srcFile, 'utf8');
        // Strip unnecessary newlines and leading spaces inside multi-line template literals for minification
        code = code.replace(/`([^`]+)`/g, (match, inner) => {
            if (inner.includes('\n')) {
                return '`' + inner.replace(/\n\s*/g, '') + '`';
            }
            return match;
        });

        fs.writeFileSync(tempFile, code, 'utf8');
        execSync(`npx -y terser "${tempFile}" -o "${minFile}" --compress --mangle`, { stdio: 'pipe' });
        console.log(` Minified: ${path.relative(path.join(__dirname, '..'), srcFile)} -> ${path.relative(path.join(__dirname, '..'), minFile)}`);
        minifiedCount++;
    } catch (e) {
        console.error(` Failed to minify ${srcFile}:`, e.message);
    } finally {
        if (fs.existsSync(tempFile)) {
            fs.unlinkSync(tempFile);
        }
    }
}

console.log(`\nCompleted! Total JS files formatted and minified: ${minifiedCount}`);

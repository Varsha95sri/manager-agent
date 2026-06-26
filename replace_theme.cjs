const fs = require('fs');
const path = require('path');

function processDirectory(dirPath) {
    const files = fs.readdirSync(dirPath);
    for (const file of files) {
        const fullPath = path.join(dirPath, file);
        if (fs.statSync(fullPath).isDirectory()) {
            processDirectory(fullPath);
        } else if (fullPath.endsWith('.blade.php') || fullPath.endsWith('.jsx')) {
            processFile(fullPath);
        }
    }
}

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // Backgrounds
    content = content.replace(/bg-slate-900\/[0-9]+/g, 'bg-white border-slate-200');
    content = content.replace(/bg-slate-950\/[0-9]+/g, 'bg-white border-slate-200');
    content = content.replace(/bg-slate-900/g, 'bg-slate-50');
    content = content.replace(/bg-slate-950/g, 'bg-white');
    content = content.replace(/bg-slate-800/g, 'bg-slate-100');
    content = content.replace(/bg-gray-900/g, 'bg-gray-50');
    content = content.replace(/bg-gray-800/g, 'bg-gray-100');

    // Text Colors (for slate text)
    content = content.replace(/text-slate-400/g, 'text-slate-600');
    content = content.replace(/text-slate-300/g, 'text-slate-700');
    content = content.replace(/text-slate-200/g, 'text-slate-800');
    content = content.replace(/text-slate-100/g, 'text-slate-900');
    content = content.replace(/text-gray-400/g, 'text-gray-600');
    content = content.replace(/text-gray-300/g, 'text-gray-700');
    content = content.replace(/text-gray-200/g, 'text-gray-800');

    // Borders
    content = content.replace(/border-slate-800/g, 'border-slate-300');
    content = content.replace(/border-slate-700/g, 'border-slate-300');
    content = content.replace(/border-gray-800/g, 'border-gray-300');
    content = content.replace(/border-gray-700/g, 'border-gray-300');

    // Dark variants
    content = content.replace(/dark:bg-gray-900/g, 'bg-gray-50');
    content = content.replace(/dark:bg-gray-800/g, 'bg-white');
    content = content.replace(/dark:border-gray-700/g, 'border-gray-300');
    content = content.replace(/dark:text-white/g, 'text-gray-900');
    content = content.replace(/dark:text-gray-400/g, 'text-gray-600');
    content = content.replace(/dark:text-gray-300/g, 'text-gray-700');

    // Handle text-white safely: 
    // We only want to replace text-white if it's NOT in a button or badge with a dark background.
    // Instead of replacing 'text-white' blindly, let's replace text-white when it's next to things like `h2`, `h3`, `p`, or when it's just formatting a heading.
    content = content.replace(/text-white mb-/g, 'text-slate-900 mb-');
    content = content.replace(/text-white font-outfit/g, 'text-slate-900 font-outfit');
    content = content.replace(/font-outfit text-white/g, 'font-outfit text-slate-900');
    content = content.replace(/class="text-white/g, 'class="text-slate-900');
    content = content.replace(/'text-white'/g, "'text-slate-900'");

    if (content !== original) {
        fs.writeFileSync(filePath, content);
        console.log(`Updated ${filePath}`);
    }
}

processDirectory('d:/manager Agent/manager-agent/resources/views');
processDirectory('d:/manager Agent/manager-agent/resources/js/Pages');
console.log('Theme replacement complete.');

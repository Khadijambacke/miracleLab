const fs = require('fs');

const indexHtml = fs.readFileSync('c:/missmiracle/index.html.bak', 'utf8').split('\n');

const ingredientSheetsStr = indexHtml.slice(2349, 2382).map(l => l.replace(/^    /, '')).join('\n');
const libraryStr = indexHtml.slice(2386, 2792).map(l => l.replace(/^    /, '')).join('\n');

const calcJsPath = 'c:/missmiracle/frontend/js/calculator.js';
let calcJs = fs.readFileSync(calcJsPath, 'utf8');

// Replace LIBRARY in calculator.js
calcJs = calcJs.replace(/const LIBRARY = \{[\s\S]*?\n\};\n/, libraryStr + '\n');

// Replace INGREDIENT_SHEETS in calculator.js
calcJs = calcJs.replace(/const INGREDIENT_SHEETS = \{[\s\S]*?\n\};\n/, ingredientSheetsStr + '\n');

fs.writeFileSync(calcJsPath, calcJs);
console.log('Successfully updated calculator.js');


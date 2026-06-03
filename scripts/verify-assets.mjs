import { readdirSync } from 'node:fs';
import { extname, join } from 'node:path';
import { spawnSync } from 'node:child_process';

const jsDir = join(process.cwd(), 'public', 'js');

function collectJsFiles(dir) {
    return readdirSync(dir, { withFileTypes: true }).flatMap((entry) => {
        const fullPath = join(dir, entry.name);

        if (entry.isDirectory()) {
            return collectJsFiles(fullPath);
        }

        return extname(entry.name) === '.js' ? [fullPath] : [];
    });
}

const jsFiles = collectJsFiles(jsDir);
let failed = false;

for (const file of jsFiles) {
    const result = spawnSync(process.execPath, ['--check', file], {
        stdio: 'inherit',
    });

    if (result.status !== 0) {
        failed = true;
    }
}

if (failed) {
    process.exit(1);
}

console.log(`Checked ${jsFiles.length} JavaScript assets.`);

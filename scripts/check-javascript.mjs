import { spawnSync } from 'node:child_process';
import { readdir } from 'node:fs/promises';
import process from 'node:process';
for (const directory of ['scripts', 'resources/js']) {
  for (const entry of await readdir(directory)) {
    if (!/\.(mjs|js)$/.test(entry)) continue;
    const result = spawnSync(process.execPath, ['--check', `${directory}/${entry}`], { stdio: 'inherit' });
    if (result.status !== 0) process.exit(result.status ?? 1);
  }
}
console.log('Sintaxis JavaScript verificada; análisis estático adicional mediante npm run check.');

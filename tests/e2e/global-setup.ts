import { execSync } from 'node:child_process';

/**
 * Reset the e2e SQLite database to a known seeded state before every run.
 * Tests should not assume mutations from earlier runs persist.
 */
export default async function globalSetup() {
    execSync('bash scripts/e2e-reset.sh', { stdio: 'inherit' });
}

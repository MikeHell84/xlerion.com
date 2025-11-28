Media worker helper scripts

- run-worker.sh: small supervisor loop that restarts the PHP worker when it exits. Use on Linux with `nohup ./run-worker.sh &` or systemd unit.
- systemd.media_worker.service: example systemd unit. Edit WorkingDirectory and ExecStart paths to match your deployment.

Windows:
- On Windows, use NSSM or a scheduled task to run `php \path\to\media_worker.php` continuously. The worker accepts `--once` to run one job and exit which is suitable for scheduled tasks.

Notes:
- The worker writes logs to `storage/logs/media_worker.log`.
- Ensure `ffmpeg` is available in PATH for video transcodes.

Tuning and metrics
- Environment variables supported:
	- MEDIA_WORKER_MAX_ATTEMPTS (default 5)
	- MEDIA_WORKER_JOB_TIMEOUT (seconds, default 120)
- The worker writes a small metrics JSON to `storage/logs/media_worker_metrics.json` containing processed/succeed/failed counters.

Concurrency
- The worker uses simple lock files under `storage/locks/` to avoid multiple workers processing the same job concurrently. Running multiple worker processes is supported; tune job timeout and attempts accordingly.

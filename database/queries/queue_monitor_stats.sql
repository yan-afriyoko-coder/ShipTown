SELECT job_class,
       count(*) as total_count,
       COUNT(CASE WHEN processing_at IS NULL THEN 1 ELSE NULL END) as currently_running,
       AVG(seconds_running) as avg_seconds_running

FROM modules_queue_monitor_jobs

GROUP BY job_class

ORDER BY avg_seconds_running DESC

LIMIT 5

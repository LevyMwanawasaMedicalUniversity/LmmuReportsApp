# LMMU Reports App Performance Optimization Guide

This guide provides recommendations for optimizing performance of the containerized LMMU Reports application, particularly for complex SQL queries and report generation.

## Docker Container Optimizations (Already Applied)

1. **PHP-FPM Configuration**
   - Increased process manager settings (pm.max_children, pm.start_servers)
   - Added request_terminate_timeout for long-running operations
   - Allocated more memory for PHP processes

2. **PHP Settings**
   - Enabled and optimized opcache
   - Increased memory_limit to 1024MB
   - Increased max_execution_time for complex reports
   - Optimized realpath cache settings

3. **SQL Server Connection Optimizations**
   - Extended connection timeouts
   - Configured persistent connections

## Database Query Optimization Recommendations

1. **Index Critical Fields**
   - Add indexes on frequently queried columns in the SQL Server database:
     ```sql
     -- Example for student account queries
     CREATE INDEX idx_account_number ON Client(Account);
     CREATE INDEX idx_postar_accountlink ON LMMU_Live.dbo.PostAR(AccountLink);
     CREATE INDEX idx_postar_txdate ON LMMU_Live.dbo.PostAR(TxDate);
     ```

2. **Optimize Frequent Student Queries**
   - Use CTEs (Common Table Expressions) for complex calculations
   - Consider materialized views for frequently accessed reports
   - Pre-filter data before applying complex calculations

3. **Query Caching**
   - Implement Laravel query caching for frequent lookups
   - Add in `config/database.php`:
     ```php
     'connections' => [
         'sqlsrv' => [
             // existing configuration...
             'options' => [
                 // Optimize SQL Server connection pooling
                 PDO::ATTR_PERSISTENT => true,
                 PDO::SQLSRV_ATTR_QUERY_TIMEOUT => 60,
                 PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE => true,
             ],
         ],
     ],
     ```

## Laravel Application Optimizations

1. **Route Caching**
   ```bash
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan view:cache
   ```

2. **Query Optimization Strategies for Student Reports**
   - Use chunking for large datasets:
     ```php
     // Instead of loading all students at once
     $students = DB::table('Client')->chunk(100, function($students) {
         foreach ($students as $student) {
             // Process student data
         }
     });
     ```

3. **Lazy Loading vs. Eager Loading**
   - Use eager loading for complex student reports:
     ```php
     // Instead of:
     $students = Student::all(); // N+1 query problem
     
     // Use:
     $students = Student::with('payments', 'registrations', 'grades')->get();
     ```

## Production Recommendations

1. **Redis Caching**
   - Add Redis for caching expensive calculations and frequent queries
   - Configure Laravel to use Redis for session and cache

2. **Database Query Monitor**
   - Install Laravel Debugbar in development to identify slow queries
   - Implement query logging for long-running reports

3. **Load Balancing**
   - For high traffic environments, consider scaling with multiple app containers

## Specific SQL Optimization Tips

For your complex payment calculations and student registration queries:

1. **Pre-calculate Payment Summaries**
   - Create a scheduled job that pre-calculates payment summaries
   - Store results in a dedicated summary table

2. **Optimize Payment Queries**
   - Move complex filtering logic into database views
   - Use temporary tables for multi-step processing

3. **Batch Processing**
   - Process large reports in batches
   - Implement background queue for generating large reports

## Monitoring Performance

1. **Container Stats**
   ```bash
   docker stats lmmu_app
   ```

2. **Profiling Slow Queries**
   ```php
   // Add to AppServiceProvider.php
   DB::listen(function($query) {
       if ($query->time > 1000) {
         Log::channel('queries')->info(
           $query->sql, 
           ['bindings' => $query->bindings, 'time' => $query->time]
         );
       }
   });
   ```

3. **Application Profiling**
   - Install Laravel Telescope for development monitoring

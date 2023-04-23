<?php

namespace Tests\Unit\Models;

use App\Models\CacheLock;
use Tests\TestCase;

class CacheLockTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_usage()
    {
        // acquire works
        $this->assertTrue(CacheLock::acquire('test'), 'Test 0 0');
        $this->assertTrue(CacheLock::acquire('test', 1), 'Test 0 1');
        $this->assertTrue(CacheLock::acquire('test', 2), 'Test 0 2');
        $this->assertTrue(CacheLock::acquire('test', 3), 'Test 0 3');

        // test if you cannot acquire second lock
        $this->assertFalse(CacheLock::acquire('test'), 'Test 1 1');
        $this->assertFalse(CacheLock::acquire('test', 1), 'Test 1 1');
        $this->assertFalse(CacheLock::acquire('test', 2), 'Test 1 2');
        $this->assertFalse(CacheLock::acquire('test', 3), 'Test 1 3');

        // release all locks
        CacheLock::release('test');
        CacheLock::release('test', 1);
        CacheLock::release('test', 2);
        CacheLock::release('test', 3);

        // test if you can acquire without issues after releasing lock
        $this->assertTrue(CacheLock::acquire('test'), 'Test 2 0');
        $this->assertTrue(CacheLock::acquire('test', 1), 'Test 2 1');
        $this->assertTrue(CacheLock::acquire('test', 2), 'Test 2 2');
        $this->assertTrue(CacheLock::acquire('test', 3), 'Test 2 3');
    }

    public function test_lock_expiration()
    {
        $this->assertTrue(CacheLock::acquire('test', 0, 1));
        sleep(2);
        $this->assertTrue(CacheLock::acquire('test'));
        $this->assertFalse(CacheLock::acquire('test'));
    }
}

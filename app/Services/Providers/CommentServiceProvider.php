<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 10/17/2018
 * Time: 12:47 PM
 */

namespace App\Services\Providers;

use App\Services\Models\Comment;
use App\Services\Repositories\Eloquent\DbCommentRepository;
use App\Services\Repositories\Interfaces\CommentInterface;
use Illuminate\Support\ServiceProvider;

class CommentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CommentInterface::class, function () {
            return new DbCommentRepository(new Comment());
        });
    }
}
<?php

namespace App\Providers;

use App\Repositories\ArticleRepository;
use App\Repositories\BannerRepository;
use App\Repositories\ConfigurationRepository;
use App\Repositories\ContactUsRepository;
use App\Repositories\DeviceTokenRepository;
use App\Repositories\FAQRepository;
use App\Repositories\InformationRepository;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\BannerRepositoryInterface;
use App\Repositories\Interfaces\ConfigurationRepositoryInterface;
use App\Repositories\Interfaces\ContactUsRepositoryInterface;
use App\Repositories\Interfaces\DeviceTokenRepositoryInterface;
use App\Repositories\Interfaces\FAQRepositoryInterface;
use App\Repositories\Interfaces\InformationRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\UserVerificationRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserVerificationRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(ConfigurationRepositoryInterface::class, ConfigurationRepository::class);
        $this->app->bind(DeviceTokenRepositoryInterface::class, DeviceTokenRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserVerificationRepositoryInterface::class, UserVerificationRepository::class);
        $this->app->bind(InformationRepositoryInterface::class, InformationRepository::class);
        $this->app->bind(BannerRepositoryInterface::class, BannerRepository::class);
        $this->app->bind(ContactUsRepositoryInterface::class, ContactUsRepository::class);
        $this->app->bind(FAQRepositoryInterface::class, FAQRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

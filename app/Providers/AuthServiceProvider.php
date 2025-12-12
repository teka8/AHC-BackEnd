<?php

namespace App\Providers;

use App\Models\ActionLog;
use App\Models\Media;
use App\Models\Module;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Term;
use App\Models\User;
use App\Models\EmailSubscription;
use App\Models\Event;
use App\Models\Venture;
use App\Models\VentureApplication;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Policies\ActionLogPolicy;
use App\Policies\MediaPolicy;
use App\Policies\ModulePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PostPolicy;
use App\Policies\RolePolicy;
use App\Policies\SettingPolicy;
use App\Policies\TermPolicy;
use App\Policies\UserPolicy;
use App\Policies\EmailSubscriptionPolicy;
use App\Policies\EventPolicy;
use App\Policies\VenturePolicy;
use App\Policies\VentureApplicationPolicy;
use App\Policies\ScholarshipPolicy;
use App\Policies\ScholarshipApplicationPolicy;
use App\Policies\ProgramPolicy;
use App\Models\Program;
use App\Policies\AhcLeaderPolicy;
use App\Models\AhcLeader;
use App\Policies\ContactMessagePolicy;
use App\Models\ContactMessage;
use App\Policies\AnalyticsPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Post::class => PostPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Term::class => TermPolicy::class,
        Media::class => MediaPolicy::class,
        Setting::class => SettingPolicy::class,
        Module::class => ModulePolicy::class,
        ActionLog::class => ActionLogPolicy::class,
        Event::class => EventPolicy::class,
        Venture::class => VenturePolicy::class,
        VentureApplication::class => VentureApplicationPolicy::class,
        Scholarship::class => ScholarshipPolicy::class,
        ScholarshipApplication::class => ScholarshipApplicationPolicy::class,
        Program::class => ProgramPolicy::class,
        EmailSubscription::class => EmailSubscriptionPolicy::class,
        AhcLeader::class => AhcLeaderPolicy::class,
        ContactMessage::class => ContactMessagePolicy::class,
        'analytics' => AnalyticsPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

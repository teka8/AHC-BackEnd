APP_NAME="AHC - AAU"
APP_ENV=local
APP_KEY=base64:SEjGcAu686ZZQORkIEodR2WdcPpqBBKjMxfIbEFoTrA=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308
DB_DATABASE=laradashboard2
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Turn this off, if your want to disable demo login filled data.
DEMO_MODE=false
SHOW_DEMO_COMPONENT_PREVIEW=false
SKIP_RECAPTCHA_IN_DEMO=false
GITHUB_LINK=https://github.com/laradashboard/laradashboard

@foreach ($posts as $post)
    @php
        // 1) Try Spatie media (featured collection)
        $featuredMedia = $post->getFirstMedia('featured');
        if ($featuredMedia) {
            $imgUrl = $featuredMedia->getUrl(); // or ->getUrl('thumb') if you have conversions
        } else {
            // 2) Fallback: extract first <img> src from the post content (if any)
            $imgUrl = null;
            if (!empty($post->content)) {
                preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->content, $matches);
                if (!empty($matches[1])) {
                    $imgUrl = $matches[1];
                }
            }

            // 3) Final fallback: placeholder image
            if (empty($imgUrl)) {
                $imgUrl = asset('images/placeholder.png'); // ensure this file exists or change path
            }
        }
    @endphp

    <div class="news-card">
        <img src="{{ $imgUrl }}" alt="{{ $post->title ?? 'News' }}" class="news-featured" />
        {{-- ...other card markup... --}}
    </div>
@endforeach

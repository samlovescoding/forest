@servers(['prolab' => 'sam@192.168.1.100'])

@setup
  $appDir = '/var/www/celebforest.com';
  $branch = 'main';
@endsetup

@story('deploy')
  pull
  composer
  frontend
  optimize
  migrate
@endstory

@task('pull')
  echo "Pulling latest from {{ $branch }}..."
  cd {{ $appDir }}
  git stash --include-untracked
  git pull origin {{ $branch }}
@endtask

@task('composer')
  echo "Installing composer dependencies..."
  cd {{ $appDir }}
  composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
@endtask

@task('frontend')
  echo "Building frontend assets..."
  export NVM_DIR="$HOME/.nvm"
  [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
  cd {{ $appDir }}
  npm ci
  npm run build
@endtask


@task('optimize')
  echo "Optimizing Laravel..."
  cd {{ $appDir }}
  php artisan optimize:clear
  php artisan optimize
@endtask

@task('migrate')
  echo "Running migrations..."
  cd {{ $appDir }}
  php artisan migrate --force
@endtask

exit
ls
php artisan tinker
php artisan tinker
xit
exit
php artisan tinker
php artisan tinker
exit
/usr/local/bin/entrypoint.sh
php artisan tinker
use App\Models\User;
User::create([
    'name'     => 'Test User',
    'email'    => 'professor@uefs.gov.br',
    'password' => bcrypt('admin123'),
]);
php artisan tinker --execute="App\Models\User::create(['name'=>'Test User','email'=>'professor@uefs.gov.br','password'=>bcrypt('admin123')])"
php artisan tinker
exit
  docker exec -it laravel bash
 /usr/local/bin/entrypoint.sh
npm run build
exit
rm -rf vendor
rm -rf node_modules
exit
php artisan test
php artisan test
php artisan test
php artisan test
php artisan test
php artisan migrate:fresh --seed
php artisan test
php artisan test
php artisan test
php artisan test
php artisan test --coverage
exit

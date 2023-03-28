<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\{Municipality, User};
use Spatie\Permission\Models\{Role, Permission};

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'super-admin',
            'admin',
            // 'appraiser',
            'cashier',
            // 'IT'
        ];

        $permissions = [
            //user management
            'view-user-list',
            'add-user',
            'edit-user',
            'delete-user',

            //role & permission
            'view-role-list',
            'add-role',
            'edit-role',
            'delete-role',

            //products
            'view-product-list',
            'add-product',
            'edit-product',
            'delete-product',

            //categories
            'view-category-list',
            'add-category',
            'edit-category',
            'delete-category',

            //supplier
            'view-supplier-list',
            'add-supplier',
            'edit-supplier',
            'delete-supplier',

            //order
            'view-order-list',
            'add-order',
            'edit-order',
            'delete-order',

            //purchase
            'view-purchase-list',
            'add-purchase',
            'edit-purchase',
            'delete-purchase',
            
            //reports
            // 'generate-reports-assessment-roll',
            // 'generate-reports-journal-of-assessed-transaction(JAT)',
            // 'generate-reports-journal-of-cancelled-transaction(JCA)',
            // 'generate-reports-ownership-record-form(ORF)',
            // 'generate-reports-property-record-form(PRF)',
            // 'generate-reports-real-property-tax-order-of-payment(RPTOP)',
            // 'generate-reports-tax-declaration-of-real-property(TDRP)',
            // 'generate-reports-tax-map-control-roll(TMCR)',
            // 'generate-reports-record-of-assessment(ROA)',

        ];

        $admin_exception = [
            // //user management
            // 'view-user-list',
            // 'add-user',
            // 'edit-user',
            // 'delete-user',
            // //role & permission
            // 'view-role-list',
            // 'add-role',
            // 'edit-role',
            // 'delete-role'
        ];

        $encoder_exception = [
            'revision',
            'user',
            'role',
            'reports',
            'statistics',
            'system_setting'
        ];

        $cashier_exception = [
            'edit-user',
            'delete-user',
            'edit-role',
            'delete-role',
            'edit-product',
            'delete-product',
            'edit-purchase',
            'delete-purchase',
            'edit-order',
            'delete-order',
            'edit-supplier',
            'delete-supplier',
            'edit-category',
            'delete-category',
            'add-role',
            'add-user',
            'add-category',
        ];

        foreach($permissions as $permit) Permission::create(['name' => $permit, 'guard_name' => 'sanctum']);

        foreach($roles as $role_key=>$role) {
            // $role = Role::create(['name' => $role, 'description' => $this->random_words(rand(3,7), rand(3,10)), 'guard_name' => 'sanctum']);
            $role = Role::create(['name' => $role, 'description' => str_replace("-", " ", ucfirst($role)),'guard_name' => 'sanctum']);
            if ($role_key == 0 || $role_key == 1) {
                foreach($permissions as $key=>$value) {
                    $permission = Permission::find($key+1);
                    $role->givePermissionTo($permission->name);
                    $permission->assignRole($role->name);
                }
            } 
            // else if($role_key == 1) {
            //     foreach($permissions as $key=>$value) {
            //         $permission = Permission::find($key+1);
            //         if (!in_array($permission->name, $admin_exception)) {
            //             $role->givePermissionTo($permission->name);
            //             $permission->assignRole($role->name);
            //         }
            //     }
            // } 
            else {
                // foreach($roles as $key=>$value) {
                //     $permission = Permission::find(rand(1,count($permissions)));
                //     $role->givePermissionTo($permission->name);
                //     $permission->assignRole($role->name);
                // }

                foreach($permissions as $key=>$value) {
                    $permission = Permission::find($key+1);
                    if (!in_array($permission->name, $cashier_exception)) {
                        $role->givePermissionTo($permission->name);
                        $permission->assignRole($role->name);
                    }
                }
            }
        }

        foreach(User::get() as $key=>$value) {
            switch ($key+1) {
                case 1: User::find(1)->assignRole('super-admin'); break;
                case 2: User::find(2)->assignRole('admin'); break;
                case 3: User::find(3)->assignRole('cashier'); break;
                // case 5: User::find(5)->assignRole($roles[rand(2, 3)]); break;
                // case 38: User::find(38)->assignRole('super-admin'); break;
                // case 39: User::find(39)->assignRole('super-admin'); break;
                // case 40: User::find(40)->assignRole('super-admin'); break;
                // case 41: User::find(41)->assignRole('super-admin'); break;
                // case 42: User::find(42)->assignRole('super-admin'); break;
            }
        }

    }

}

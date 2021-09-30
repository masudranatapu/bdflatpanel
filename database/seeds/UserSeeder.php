<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Empty all ACL related table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permission_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('admin_users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('auths')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('auth_role')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permission')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //Make data array
        $user_data = [
            [
                'group_name' => 'Permission group',
                'status' => '1',
                'permissions' => [
                    [
                        'name' => 'view_permission_group',
                        'display_name'  => 'View',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'add_permission_group',
                        'display_name'  => 'Add',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'edit_permission_group',
                        'display_name'  => 'Edit',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'delete_permission_group',
                        'display_name'  => 'Delete',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'execute_permission_group',
                        'display_name'  => 'Execute',
                        'status'  => 1,
                    ]
                ]
            ],

            [
                'group_name' => 'Permission',
                'status' => '1',
                'permissions' => [
                    [
                        'name' => 'view_permission',
                        'display_name'  => 'View',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'add_permission',
                        'display_name'  => 'Add',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'edit_permission',
                        'display_name'  => 'Edit',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'delete_permission',
                        'display_name'  => 'Delete',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'execute_delete_permission',
                        'display_name'  => 'Execute',
                        'status'  => 1,
                    ]
                ]
            ],

            [
                'group_name' => 'User role',
                'status' => '1',
                'permissions' => [
                    [
                        'name' => 'view_role',
                        'display_name'  => 'View',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'add_role',
                        'display_name'  => 'Add',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'edit_role',
                        'display_name'  => 'Edit',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'delete_role',
                        'display_name'  => 'Delete',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'execute_role',
                        'display_name'  => 'Execute',
                        'status'  => 1,
                    ]
                ]
            ],

            [
                'group_name' => 'Dashboard',
                'status' => '1',
                'permissions' => [
                    [
                        'name' => 'view_dashboard',
                        'display_name'  => 'View',
                        'status'  => 1,
                    ],
                    [
                        'name' => '',
                        'display_name'  => 'Add',
                        'status'  => 1,
                    ],
                    [
                        'name' => '',
                        'display_name'  => 'Edit',
                        'status'  => 1,
                    ],
                    [
                        'name' => '',
                        'display_name'  => 'Delete',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'execute_dashboard',
                        'display_name'  => 'Execute',
                        'status'  => 1,
                    ]
                ]
            ],

            [
                'group_name' => 'Admin User',
                'status' => '1',
                'permissions' => [
                    [
                        'name' => 'view_admin_user',
                        'display_name'  => 'View',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'add_admin_user',
                        'display_name'  => 'Add',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'edit_admin_user',
                        'display_name'  => 'Edit',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'delete_admin_user',
                        'display_name'  => 'Delete',
                        'status'  => 1,
                    ],
                    [
                        'name' => 'execute_admin_user',
                        'display_name'  => 'Execute',
                        'status'  => 1,
                    ]
                ]
            ]
        ];

        foreach($user_data as $data)
        {
            $id = DB::table('permission_groups')->insertGetId(
                [
                    'group_name'    => $data['group_name'],
                    'status'        => $data['status'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]
            );

            foreach($data['permissions'] as $val)
            {
                DB::table('permissions')->insert([
                    [
                        'name'          => $val['name'],
                        'display_name'  => $val['display_name'],
                        'permission_group_id'  => $id,
                        'status'        => $val['status'],
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ]
                ]);
            }
        }

        $role_id = DB::table('roles')->insertGetId(
            [
                'role_name'     => 'Super admin',
                'status'        => 1,
                'created_by'    => 1,
                'edited_by'     => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]
        );

        //Make super admin user
        $auth_id = DB::table('auths')->insertGetId(
            [
                'email'         => 'admin@admin.com',
                'password'      => bcrypt('123456'),
                'mobile_no'     => '01016000000',
                'model_id'      => 1,
                'user_type'     => 0,
                'can_login'     => 1,
                'status'        => 1
            ]
        );

        //Basic info of admin user
        DB::table('admin_users')->insertGetId(
            [
                'first_name'    => 'Super',
                'last_name'     => 'Admin',
                'designation'   => 'Super Admin',
                'auth_id'       => $auth_id,
                'status'        => 1
            ]
        );

        //Auth Role relation
        DB::table('auth_role')->insert(
            [
                'auth_id'       => $auth_id,
                'role_id'       => $role_id,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]
        );

        //Role permission relation
        $role_permissions = [
            [
                'permissions'   => ',view_dashboard,',
                'role_id'       => 1
            ],
        ];

        foreach($role_permissions as $role_permission)
        {
            DB::table('role_permission')->insert(
                [
                    'permissions'   => $role_permission['permissions'],
                    'role_id'       => $role_permission['role_id'],
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ]
            );
        }
    }
}

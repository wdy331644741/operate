<?php 
return array (
  'admin_node' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'lenght' => '8',
      'unsigned' => true,
      'null' => false,
    ),
    'controller' => 
    array (
      'field' => 'controller',
      'key' => '',
      'default' => '',
      'lenght' => '80',
      'unsigned' => false,
      'null' => true,
    ),
    'action' => 
    array (
      'field' => 'action',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => '',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'parent_id' => 
    array (
      'field' => 'parent_id',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'url_host' => 
    array (
      'field' => 'url_host',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '1',
      'type' => 'int',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'admin_role' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'lenght' => '8',
      'unsigned' => true,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => '',
      'default' => '',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '200',
      'unsigned' => false,
      'null' => true,
    ),
    'rule' => 
    array (
      'field' => 'rule',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '600',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'admin_user' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => 'UNI',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'password' => 
    array (
      'field' => 'password',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => NULL,
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'is_form' => 
    array (
      'field' => 'is_form',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'role_id' => 
    array (
      'field' => 'role_id',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '6',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'last_time' => 
    array (
      'field' => 'last_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'last_ip' => 
    array (
      'field' => 'last_ip',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'admin_user_role' => 
  array (
    'role_id' => 
    array (
      'field' => 'role_id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'id_number' => 
    array (
      'field' => 'id_number',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => true,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => true,
    ),
    'select_type' => 
    array (
      'field' => 'select_type',
      'key' => '',
      'default' => '2',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'role_id',
  ),
  'auth_user' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'username' => 
    array (
      'field' => 'username',
      'key' => 'UNI',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => '',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'password' => 
    array (
      'field' => 'password',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '128',
      'unsigned' => false,
      'null' => false,
    ),
    'trade_pwd' => 
    array (
      'field' => 'trade_pwd',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '128',
      'unsigned' => false,
      'null' => true,
    ),
    'first_name' => 
    array (
      'field' => 'first_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'last_name' => 
    array (
      'field' => 'last_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'realname' => 
    array (
      'field' => 'realname',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => true,
    ),
    'id_number' => 
    array (
      'field' => 'id_number',
      'key' => 'MUL',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'display_name' => 
    array (
      'field' => 'display_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'gender' => 
    array (
      'field' => 'gender',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'birthday' => 
    array (
      'field' => 'birthday',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'email' => 
    array (
      'field' => 'email',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '75',
      'unsigned' => false,
      'null' => true,
    ),
    'from_user_id' => 
    array (
      'field' => 'from_user_id',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'invite_code' => 
    array (
      'field' => 'invite_code',
      'key' => 'MUL',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'from_channel' => 
    array (
      'field' => 'from_channel',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => true,
    ),
    'from_platform' => 
    array (
      'field' => 'from_platform',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'system' => 
    array (
      'field' => 'system',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'last_login' => 
    array (
      'field' => 'last_login',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'last_ip' => 
    array (
      'field' => 'last_ip',
      'key' => '',
      'default' => 'unkown',
      'type' => 'varchar',
      'lenght' => '15',
      'unsigned' => false,
      'null' => true,
    ),
    'ip_area' => 
    array (
      'field' => 'ip_area',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'is_active' => 
    array (
      'field' => 'is_active',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'is_company' => 
    array (
      'field' => 'is_company',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'level' => 
    array (
      'field' => 'level',
      'key' => '',
      'default' => '-1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => true,
    ),
    'score' => 
    array (
      'field' => 'score',
      'key' => '',
      'default' => '0',
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => 'MUL',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'award_experience' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '64',
      'unsigned' => false,
      'null' => false,
    ),
    'amount_type' => 
    array (
      'field' => 'amount_type',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => true,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => '0',
      'unsigned' => true,
      'null' => false,
    ),
    'min_amount' => 
    array (
      'field' => 'min_amount',
      'key' => '',
      'default' => '0',
      'unsigned' => true,
      'null' => false,
    ),
    'max_amount' => 
    array (
      'field' => 'max_amount',
      'key' => '',
      'default' => '0',
      'unsigned' => true,
      'null' => false,
    ),
    'days' => 
    array (
      'field' => 'days',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => true,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => true,
    ),
    'limit_node' => 
    array (
      'field' => 'limit_node',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'repeat' => 
    array (
      'field' => 'repeat',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'award_interestcoupon' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'rate' => 
    array (
      'field' => 'rate',
      'key' => '',
      'default' => '0.0000',
      'unsigned' => true,
      'null' => true,
    ),
    'days' => 
    array (
      'field' => 'days',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => true,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'limit_node' => 
    array (
      'field' => 'limit_node',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'coupon' => 
    array (
      'field' => 'coupon',
      'key' => '',
      'default' => NULL,
      'lenght' => '50',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'award_node' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'award_withdraw' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'times' => 
    array (
      'field' => 'times',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '3',
      'unsigned' => false,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'limit_node' => 
    array (
      'field' => 'limit_node',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => false,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'config_earnings' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'desc' => 
    array (
      'field' => 'desc',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'start_time' => 
    array (
      'field' => 'start_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'end_time' => 
    array (
      'field' => 'end_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => true,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'head_count' => 
    array (
      'field' => 'head_count',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'marketing_activity' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'img_url' => 
    array (
      'field' => 'img_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'link_url' => 
    array (
      'field' => 'link_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'sort' => 
    array (
      'field' => 'sort',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => true,
    ),
    'start_time' => 
    array (
      'field' => 'start_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'end_time' => 
    array (
      'field' => 'end_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'desc' => 
    array (
      'field' => 'desc',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => true,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_article' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'img_url' => 
    array (
      'field' => 'img_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'cate_node' => 
    array (
      'field' => 'cate_node',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'content' => 
    array (
      'field' => 'content',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'sort' => 
    array (
      'field' => 'sort',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => true,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_article_log' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'article_id' => 
    array (
      'field' => 'article_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'last_time' => 
    array (
      'field' => 'last_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'counts' => 
    array (
      'field' => 'counts',
      'key' => '',
      'default' => '1',
      'type' => 'int',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '200',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'marketing_article_node' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'name' => 
    array (
      'field' => 'name',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_banner' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'title' => 
    array (
      'field' => 'title',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'img_url' => 
    array (
      'field' => 'img_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'link_url' => 
    array (
      'field' => 'link_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => false,
    ),
    'pos' => 
    array (
      'field' => 'pos',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'sort' => 
    array (
      'field' => 'sort',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => true,
    ),
    'start_time' => 
    array (
      'field' => 'start_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'end_time' => 
    array (
      'field' => 'end_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_checkin' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'continue_days' => 
    array (
      'field' => 'continue_days',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'marketing_experience' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => 'UNI',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'source_id' => 
    array (
      'field' => 'source_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'source_name' => 
    array (
      'field' => 'source_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => true,
      'null' => false,
    ),
    'effective_start' => 
    array (
      'field' => 'effective_start',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'continuous_days' => 
    array (
      'field' => 'continuous_days',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => true,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => true,
    ),
    'is_activate' => 
    array (
      'field' => 'is_activate',
      'key' => '',
      'default' => '0',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'is_use' => 
    array (
      'field' => 'is_use',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'marketing_interestcoupon' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => 'UNI',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'source_id' => 
    array (
      'field' => 'source_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'source_name' => 
    array (
      'field' => 'source_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => false,
    ),
    'rate' => 
    array (
      'field' => 'rate',
      'key' => '',
      'default' => '0.0000',
      'unsigned' => true,
      'null' => true,
    ),
    'effective_start' => 
    array (
      'field' => 'effective_start',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'continuous_days' => 
    array (
      'field' => 'continuous_days',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => true,
      'null' => false,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '250',
      'unsigned' => false,
      'null' => true,
    ),
    'is_activate' => 
    array (
      'field' => 'is_activate',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => false,
    ),
    'is_use' => 
    array (
      'field' => 'is_use',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'marketing_revenuesharing' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'from_user_id' => 
    array (
      'field' => 'from_user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => true,
    ),
    'type' => 
    array (
      'field' => 'type',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '20',
      'unsigned' => false,
      'null' => true,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'cash_total' => 
    array (
      'field' => 'cash_total',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'interest_coupon_total' => 
    array (
      'field' => 'interest_coupon_total',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'start_time' => 
    array (
      'field' => 'start_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'end_time' => 
    array (
      'field' => 'end_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '100',
      'type' => 'int',
      'lenght' => '5',
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'rate' => 
    array (
      'field' => 'rate',
      'key' => '',
      'default' => '100.0000',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'marketing_withdrawcoupon' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'uuid' => 
    array (
      'field' => 'uuid',
      'key' => 'UNI',
      'default' => NULL,
      'lenght' => '36',
      'unsigned' => false,
      'null' => false,
    ),
    'source_id' => 
    array (
      'field' => 'source_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'source_name' => 
    array (
      'field' => 'source_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => false,
    ),
    'effective_start' => 
    array (
      'field' => 'effective_start',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'order_id' => 
    array (
      'field' => 'order_id',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '64',
      'unsigned' => false,
      'null' => true,
    ),
    'limit_desc' => 
    array (
      'field' => 'limit_desc',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '250',
      'unsigned' => false,
      'null' => true,
    ),
    'is_activate' => 
    array (
      'field' => 'is_activate',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => false,
    ),
    'is_use' => 
    array (
      'field' => 'is_use',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => true,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'promoter_list' => 
  array (
    'apply_id' => 
    array (
      'field' => 'apply_id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'auth_id' => 
    array (
      'field' => 'auth_id',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
    ),
    'username' => 
    array (
      'field' => 'username',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'phone' => 
    array (
      'field' => 'phone',
      'key' => '',
      'default' => NULL,
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'invite_num' => 
    array (
      'field' => 'invite_num',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '5',
      'unsigned' => false,
      'null' => false,
    ),
    'total_inve_amount' => 
    array (
      'field' => 'total_inve_amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'commission' => 
    array (
      'field' => 'commission',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => false,
    ),
    'level' => 
    array (
      'field' => 'level',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => true,
    ),
    'earnings' => 
    array (
      'field' => 'earnings',
      'key' => '',
      'default' => '20.00',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'apply_id',
  ),
  'sms_log' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'mobile' => 
    array (
      'field' => 'mobile',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '30',
      'unsigned' => false,
      'null' => false,
    ),
    'contents' => 
    array (
      'field' => 'contents',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => false,
    ),
    'created_at' => 
    array (
      'field' => 'created_at',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'sms_template' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'node_name' => 
    array (
      'field' => 'node_name',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'content_tpl' => 
    array (
      'field' => 'content_tpl',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '300',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => '',
      'default' => 'CURRENT_TIMESTAMP',
      'unsigned' => false,
      'null' => true,
    ),
    'des' => 
    array (
      'field' => 'des',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '200',
      'unsigned' => false,
      'null' => false,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'user_award_counts' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => true,
      'null' => false,
    ),
    'award_type' => 
    array (
      'field' => 'award_type',
      'key' => 'MUL',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '25',
      'unsigned' => false,
      'null' => true,
    ),
    'award_nums' => 
    array (
      'field' => 'award_nums',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '8',
      'unsigned' => true,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
);
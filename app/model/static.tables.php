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
    'hours' => 
    array (
      'field' => 'hours',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
      'unsigned' => false,
      'null' => true,
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
    'experience_name' => 
    array (
      'field' => 'experience_name',
      'key' => '',
      'default' => NULL,
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'award_extend' => 
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
    'user' => 
    array (
      'field' => 'user',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'award_type' => 
    array (
      'field' => 'award_type',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => true,
      'null' => false,
    ),
    'award_id' => 
    array (
      'field' => 'award_id',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'remark' => 
    array (
      'field' => 'remark',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => true,
    ),
    'send_count' => 
    array (
      'field' => 'send_count',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => true,
      'null' => false,
    ),
    'send_status' => 
    array (
      'field' => 'send_status',
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
      'default' => '0000-00-00 00:00:00',
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
  'award_hand_record' => 
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
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => '',
      'default' => '0',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'award_extend_id' => 
    array (
      'field' => 'award_extend_id',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
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
    'mark' => 
    array (
      'field' => 'mark',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'ctime' => 
    array (
      'field' => 'ctime',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'utime' => 
    array (
      'field' => 'utime',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'award_type' => 
    array (
      'field' => 'award_type',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'award_id' => 
    array (
      'field' => 'award_id',
      'key' => '',
      'default' => '0',
      'lenght' => '20',
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
    'effective_days' => 
    array (
      'field' => 'effective_days',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'effective_start' => 
    array (
      'field' => 'effective_start',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'effective_end' => 
    array (
      'field' => 'effective_end',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
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
    'contact_activity' => 
    array (
      'field' => 'contact_activity',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
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
  'award_redpacket' => 
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
      'lenght' => '32',
      'unsigned' => false,
      'null' => false,
    ),
    'redpacketet_type' => 
    array (
      'field' => 'redpacketet_type',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
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
    'min_amount' => 
    array (
      'field' => 'min_amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'max_amount' => 
    array (
      'field' => 'max_amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'max_split' => 
    array (
      'field' => 'max_split',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '3',
      'unsigned' => false,
      'null' => true,
    ),
    'usetime_start' => 
    array (
      'field' => 'usetime_start',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'usetime_end' => 
    array (
      'field' => 'usetime_end',
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
      'lenght' => '32',
      'unsigned' => false,
      'null' => true,
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
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => false,
    ),
    'is_del' => 
    array (
      'field' => 'is_del',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => false,
    ),
    'repeat' => 
    array (
      'field' => 'repeat',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'day_repeat' => 
    array (
      'field' => 'day_repeat',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
    ),
    'max_counts' => 
    array (
      'field' => 'max_counts',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => false,
    ),
    'redpacket_name' => 
    array (
      'field' => 'redpacket_name',
      'key' => '',
      'default' => NULL,
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
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
    'withdraw_name' => 
    array (
      'field' => 'withdraw_name',
      'key' => '',
      'default' => NULL,
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
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
    'percent' => 
    array (
      'field' => 'percent',
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
  'gloab_config' => 
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
    'key' => 
    array (
      'field' => 'key',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'value' => 
    array (
      'field' => 'value',
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
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
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
    'check_login' => 
    array (
      'field' => 'check_login',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => true,
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
    'activity_name' => 
    array (
      'field' => 'activity_name',
      'key' => '',
      'default' => NULL,
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
    ),
    'conf_json' => 
    array (
      'field' => 'conf_json',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '100',
      'unsigned' => false,
      'null' => true,
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
    'continuous_hours' => 
    array (
      'field' => 'continuous_hours',
      'key' => '',
      'default' => '0',
      'lenght' => '5',
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
  'marketing_index' => 
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
      'null' => true,
    ),
    'link_url' => 
    array (
      'field' => 'link_url',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '150',
      'unsigned' => false,
      'null' => true,
    ),
    'pos' => 
    array (
      'field' => 'pos',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'display_name' => 
    array (
      'field' => 'display_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '50',
      'unsigned' => false,
      'null' => false,
    ),
    'start_time' => 
    array (
      'field' => 'start_time',
      'key' => 'MUL',
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
    'check_login' => 
    array (
      'field' => 'check_login',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '1',
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
    'usetime_start' => 
    array (
      'field' => 'usetime_start',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'usetime_end' => 
    array (
      'field' => 'usetime_end',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
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
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
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
    'type' => 
    array (
      'field' => 'type',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '3',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'marketing_redpactek' => 
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
    'accept_userid' => 
    array (
      'field' => 'accept_userid',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
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
      'unsigned' => false,
      'null' => false,
    ),
    'source_name' => 
    array (
      'field' => 'source_name',
      'key' => '',
      'default' => NULL,
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => false,
    ),
    'amount' => 
    array (
      'field' => 'amount',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'usetime_start' => 
    array (
      'field' => 'usetime_start',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'usetime_end' => 
    array (
      'field' => 'usetime_end',
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
      'lenght' => '32',
      'unsigned' => false,
      'null' => false,
    ),
    'type' => 
    array (
      'field' => 'type',
      'key' => '',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '2',
      'unsigned' => false,
      'null' => false,
    ),
    'effective_start' => 
    array (
      'field' => 'effective_start',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => false,
    ),
    'is_activate' => 
    array (
      'field' => 'is_activate',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => false,
    ),
    'is_used' => 
    array (
      'field' => 'is_used',
      'key' => 'MUL',
      'default' => NULL,
      'type' => 'tinyint',
      'lenght' => '1',
      'unsigned' => false,
      'null' => false,
    ),
    'create_time' => 
    array (
      'field' => 'create_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'update_time' => 
    array (
      'field' => 'update_time',
      'key' => 'MUL',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'be_invite_id' => 
    array (
      'field' => 'be_invite_id',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => false,
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
    'earnings_id' => 
    array (
      'field' => 'earnings_id',
      'key' => '',
      'default' => NULL,
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'apply_id',
  ),
  'promoter_statistics' => 
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
    'date' => 
    array (
      'field' => 'date',
      'key' => '',
      'default' => NULL,
      'unsigned' => false,
      'null' => true,
    ),
    'adds' => 
    array (
      'field' => 'adds',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'register_adds' => 
    array (
      'field' => 'register_adds',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'recharge_adds' => 
    array (
      'field' => 'recharge_adds',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'withdraw_adds' => 
    array (
      'field' => 'withdraw_adds',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '10',
      'unsigned' => false,
      'null' => true,
    ),
    'pk_name' => 'id',
  ),
  'redeem_code' => 
  array (
    'id' => 
    array (
      'field' => 'id',
      'key' => 'PRI',
      'default' => NULL,
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'user_id' => 
    array (
      'field' => 'user_id',
      'key' => 'MUL',
      'default' => '0',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'code' => 
    array (
      'field' => 'code',
      'key' => 'UNI',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '32',
      'unsigned' => false,
      'null' => true,
    ),
    'redeem_time' => 
    array (
      'field' => 'redeem_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'ctime' => 
    array (
      'field' => 'ctime',
      'key' => '',
      'default' => 'CURRENT_TIMESTAMP',
      'unsigned' => false,
      'null' => true,
    ),
    'type' => 
    array (
      'field' => 'type',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'map_id' => 
    array (
      'field' => 'map_id',
      'key' => '',
      'default' => '0',
      'lenght' => '20',
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
    'redeem_sn' => 
    array (
      'field' => 'redeem_sn',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'meta_id' => 
    array (
      'field' => 'meta_id',
      'key' => 'MUL',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'pk_name' => 'id',
  ),
  'redeem_code_meta' => 
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
    'name' => 
    array (
      'field' => 'name',
      'key' => '',
      'default' => '',
      'type' => 'varchar',
      'lenght' => '255',
      'unsigned' => false,
      'null' => true,
    ),
    'map_id' => 
    array (
      'field' => 'map_id',
      'key' => '',
      'default' => '0',
      'lenght' => '20',
      'unsigned' => false,
      'null' => false,
    ),
    'type' => 
    array (
      'field' => 'type',
      'key' => '',
      'default' => '0',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => false,
    ),
    'total' => 
    array (
      'field' => 'total',
      'key' => '',
      'default' => '0',
      'type' => 'int',
      'lenght' => '11',
      'unsigned' => false,
      'null' => false,
    ),
    'user_max_get' => 
    array (
      'field' => 'user_max_get',
      'key' => '',
      'default' => '0',
      'lenght' => '6',
      'unsigned' => false,
      'null' => false,
    ),
    'start_time' => 
    array (
      'field' => 'start_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'end_time' => 
    array (
      'field' => 'end_time',
      'key' => '',
      'default' => '0000-00-00 00:00:00',
      'unsigned' => false,
      'null' => true,
    ),
    'status' => 
    array (
      'field' => 'status',
      'key' => '',
      'default' => '1',
      'type' => 'tinyint',
      'lenght' => '4',
      'unsigned' => false,
      'null' => true,
    ),
    'ctime' => 
    array (
      'field' => 'ctime',
      'key' => '',
      'default' => 'CURRENT_TIMESTAMP',
      'unsigned' => false,
      'null' => true,
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
    'pk_name' => 'id',
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
#!/bin/bash

#自定义内容
APP_PATH=/home/qingchengzhixiao
APP_NAME=easyswoole
LOG_NAME=/var/log/easyswoole.log
SYS_CONF_BAK=/home/backdir

PHP_INI=/usr/local/php/etc/php.ini
PHP_FPM=/usr/local/php/etc/php-fpm.conf

IPT_FILE=/etc/sysconfig/iptables
IPT=/sbin/iptables

MYSQL_IP=127.0.0.1
MYSQL_PORT=3306
MYSQL_ADMIN_USER=root
MYSQL_ADMIN_PWD=root
MYSQL_CMD="mysql -h${MYSQL_IP} -P${MYSQL_PORT} -u${MYSQL_ADMIN_USER} -p${MYSQL_ADMIN_PWD}"

#执行命令有误时，提示使用说明参数
usage() {
  echo "Usage: sh app.sh [install|start|stop|restart|status]"
  exit 1
}

#检查程序是否已经在运行
is_exist() {
  pid=$(ps -ef | grep $APP_NAME | grep -v grep | awk '{print $2}')
  if [ -z "${pid}" ]; then
    return 1
  else
    return 0
  fi
}

#安装服务
install() {
  is_exist
  if [ $? -eq "0" ]; then
    echo "${APP_NAME} is already install."
  else
    modify_app_permission
    modify_app_runtime
    firewall_config
    init_database
    app_start_with_boot
  fi
}

#改变应用文件权限
modify_app_permission() {
  echo ">>>>>>>> Start Change Application Files Permission >>>>>>>>"
  #获取默认的虚拟目录
  if [ ! -d "${APP_PATH}" ]; then
    APP_PATH="/home/smart-campus-php"

    chmod -R 777 "/home"
    mkdir -p ${APP_PATH}
    chmod -R 777 ${APP_PATH}

  fi
  echo ">>>>>>>> End Change Application Files Permission >>>>>>>>"
}

#修改app运行环境
modify_app_runtime() {

  #配置文件是否存在
  if [ -f "$PHP_INI" ]; then
    #拷贝备份配置
    cp -rf ${PHP_INI} ${SYS_CONF_BAK}
    echo ">>>>>>>> backup PHP INI Config successful. >>>>>>>>"

    #配置最大运行时间
    maxexetime=$(grep "^max_execution_time *=" "$PHP_INI" | awk '{print $3}' | sed 's/"//g' | sed 's/\r//g' | sed 's/\n//g')
    if (echo $maxexetime | egrep -q '^[0-9]+$'); then
      sed -i 's/^max_execution_time *= *"*[0-9]*"*/max_execution_time = 3600/g' $PHP_INI #修改 max_execution_time 键的值
    else
      sed -i '388i max_execution_time = 3600' $PHP_INI #这是在最后一行行后添加字符串
    fi
    echo ">>>>>>>> setting PHPINI Config successful. [max_execution_time = 3600] >>>>>>>>"

    #配置安全模式 sate_mode = off
    satemode=$(grep "^sate_mode *=" "$PHP_INI" | awk '{print $3}' | sed 's/"//g' | sed 's/\r//g' | sed 's/\n//g')
    maxexetime=$(grep -n "^max_execution_time *=" "$PHP_INI" | awk -F":" '{print $1}')
    if (echo $satemode | egrep -q '^[0-9a-zA-Z]+$'); then
      sed -i 's/^sate_mode *= *"*[0-9a-zA-Z]*"*/sate_mode = off/g' $PHP_INI
    else
      eval "sed -i '${maxexetime}i sate_mode = off' ${PHP_INI}"
      #sed -i '382i sate_mode = off' $PHPINI
    fi
    echo ">>>>>>>> setting PHP INI Config successful. [sate_mode = off] >>>>>>>>"

    #配置内存 memory_limit = 320M
    memlimit=$(grep "^memory_limit *=" "$PHP_INI" | awk '{print $3}' | sed 's/"//g' | sed 's/\r//g' | sed 's/\n//g')
    if (echo $memlimit | egrep -q '^[0-9a-zA-Z]+$'); then
      sed -i 's/^memory_limit *= *"*[0-9a-zA-Z]*"*/memory_limit = 1024M/g' $PHP_INI
    else
      eval "sed -i '${maxexetime}i memory_limit = 1024M' ${PHP_INI}"
      #sed -i '382i memory_limit = 1024M' $PHPINI
    fi
    echo ">>>>>>>> setting PHPINI Config successful. [memory_limit = 1024M] >>>>>>>>"

    echo ">>>>>>>> php.ini max execution time configure completed >>>>>>>>"
  else
    echo ">>>>>>>> php.ini max execution time configure no exists >>>>>>>>"
  fi

  #配置请求超时时间
  if [ -f "$PHP_FPM" ]; then
    #拷贝备份配置
    cp -rf ${PHP_FPM} ${SYS_CONF_BAK}
    echo ">>>>>>>> backup php-fpm Config successful. >>>>>>>>"

    reqtimeout=$(grep "^request_terminate_timeout  *=" "$PHP_FPM" | awk '{print $3}' | sed 's/"//g' | sed 's/\r//g' | sed 's/\n//g')
    if (echo $reqtimeout | egrep -q '^[0-9]+$'); then
      sed -i 's/^request_terminate_timeout  *= *"*[0-9]*"*/request_terminate_timeout = 3600/g' $PHP_FPM
    else
      sed -i '$a request_terminate_timeout = 3600' $PHP_FPM
    fi
    echo ">>>>>>>> setting php-fpm Config successful. [request_terminate_timeout = 3600] >>>>>>>>"

    echo ">>>>>>>> php-fpm.conf request terminate timeout configure completed >>>>>>>>"
  else
    echo ">>>>>>>> php-fpm.conf request terminate timeout configure no exists >>>>>>>>"
  fi
}

#配置应用开机启动
app_start_with_boot() {
  echo ">>>>>>>> application startup config start >>>>>>>>"
  cp -rf "/home/smart-campus-php/easyswoole.service" "/usr/lib/systemd/system"
  systemctl enable easyswoole
  systemctl start easyswoole
  echo ">>>>>>>> application startup config end >>>>>>>>"
}

#配置防火墙
firewall_config() {
  local IPT_PORT
  #配置防火墙
  if [ -f "$IPT_FILE" ]; then
    #拷贝备份配置
    cp -rf ${IPT_FILE} ${SYS_CONF_BAK}
    echo ">>>>>>>> backup iptables Config successful. >>>>>>>>"

    IPT_PORT=$(grep "tcp --dport 21 -j *" "$IPT_FILE")
    #判断为空则添加， [-n判断不为空]
    if test -z "$IPT_PORT"; then
      $IPT -A INPUT -p tcp -m tcp --dport 21 -j ACCEPT
    fi

    IPT_PORT=$(grep "tcp --dport 22 -j *" "$IPT_FILE")
    if test -z "$IPT_PORT"; then
      $IPT -A INPUT -p tcp -m tcp --dport 22 -j ACCEPT
    fi

    IPT_PORT=$(grep "tcp --dport 80 -j *" "$IPT_FILE")
    if test -z "$IPT_PORT"; then
      $IPT -A INPUT -p tcp -m tcp --dport 80 -j ACCEPT
    fi

    IPT_PORT=$(grep "tcp --dport 3306 -j *" "$IPT_FILE")
    if test -z "$IPT_PORT"; then
      $IPT -A INPUT -p tcp -m tcp --dport 3306 -j ACCEPT
    fi

    IPT_PORT=$(grep "tcp --dport 443 -j *" "$IPT_FILE")
    if test -z "$IPT_PORT"; then
      $IPT -A INPUT -p tcp -m tcp --dport 443 -j ACCEPT
    fi

    IPT_PORT=$(grep "tcp --dport 8087 -j *" "$IPT_FILE")
    if test -z "$IPT_PORT"; then
      $IPT -A INPUT -p tcp -m tcp --dport 8087 -j ACCEPT
    fi

    service iptables save
    service iptables restart

    echo ">>>>>>>> setting iptables Config successful. [21,22,80,3306,443,8087] >>>>>>>>"

  else
    echo ">>>>>>>> iptables configure no exists >>>>>>>>"
  fi
}

init_database() {
  ${MYSQL_CMD} -e "create USER if NOT EXISTS '${mysql_user}'@'%' identified by '${mysql_pwd}';"
  ${MYSQL_CMD} -e "create SCHEMA if NOT EXISTS ${mysql_schema} default character set utf8mb4 collate utf8mb4_bin;"
  ${MYSQL_CMD} -e "grant ALL on ${schema_name}.* to '${mysql_user}'@'%';"
  mysql -h${MYSQL_IP} -P${MYSQL_PORT} -u${mysql_user} -p${mysql_pwd} ${mysql_schema} <${sql_name}
}

#启动服务 nohup $JAVA_JDK/bin/java -Duser.dir=$APP_PATH -jar $APP_NAME >$LOG_NAME 2>&1 &
start() {
  is_exist
  if [ $? -eq "0" ]; then
    echo "${APP_NAME} is already running. pid=${pid} ."
  else
    php easyswoole server start d
  fi
}

#停止服务
stop() {
  is_exist
  if [ $? -eq "0" ]; then
    kill -9 $pid
  else
    echo "${APP_NAME} is not running"
  fi
}

#输出服务运行状态
status() {
  is_exist
  if [ $? -eq "0" ]; then
    echo "${APP_NAME} is running. Pid is ${pid}"
  else
    echo "${APP_NAME} is NOT running."
  fi
}

#重启服务
restart() {
  stop
  start
}

#根据输入参数，选择执行对应的方法，不输入则执行使用说明
case "$1" in
"install")
  install
  ;;
"start")
  start
  ;;
"stop")
  stop
  ;;
"status")
  status
  ;;
"restart")
  restart
  ;;
*)
  usage
  ;;
esac

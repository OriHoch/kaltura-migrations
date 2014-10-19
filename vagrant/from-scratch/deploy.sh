#!/usr/bin/env sh
iptables -F
service iptables stop
chkconfig iptables off
setenforce permissive
rpm -ihv http://installrepo.kaltura.org/releases/kaltura-release.noarch.rpm
yum -y install mysql mysql-server
service mysqld start
mysqladmin -u root password "$DBPASS"
mysql -u root -p"$DBPASS" -e "update mysql.user set password=PASSWORD('$DBPASS') where user='root'"
mysql -u root -p"$DBPASS" -e ""
mysql -u root -p"$DBPASS" -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1')"
mysql -u root -p"$DBPASS" -e "DELETE FROM mysql.user WHERE User=''"
mysql -u root -p"$DBPASS" -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\_%'"
mysql -u root -p"$DBPASS" -e "FLUSH PRIVILEGES"
chkconfig mysqld on
service postfix restart
yum -y clean all
yum -y install kaltura-server
/opt/kaltura/bin/kaltura-mysql-settings.sh
service memcached restart
service ntpd restart
chkconfig memcached on
chkconfig ntpd on
echo 'USER_CONSENT=0' > /opt/kaltura/bin/contact.rc
echo '127.0.0.1 kaltura.local' >> /etc/hosts
echo 'TIME_ZONE="UTC"
KALTURA_FULL_VIRTUAL_HOST_NAME="kaltura.local:80"
KALTURA_VIRTUAL_HOST_NAME="kaltura.local"
DB1_HOST="127.0.0.1"
DB1_PORT="3306"
DB1_PASS="7Cq6YypdAMl9ycN"
DB1_NAME="kaltura"
DB1_USER="kaltura"
SERVICE_URL="http://kaltura.local:80"
SPHINX_SERVER1="127.0.0.1"
SPHINX_SERVER2=" "
DWH_HOST="127.0.0.1"
DWH_PORT="3306"
SPHINX_DB_HOST="127.0.0.1"
SPHINX_DB_PORT="3306"
ADMIN_CONSOLE_ADMIN_MAIL="admin@kaltura.local"
ADMIN_CONSOLE_PASSWORD="Kaltura1!"
CDN_HOST="kaltura.local"
KALTURA_VIRTUAL_HOST_PORT="80"
SUPER_USER="root"
SUPER_USER_PASSWD="vagrant"
ENVIRONMENT_NAME="Kaltura Local"
DWH_PASS="7Cq6YypdAMl9ycN"
PROTOCOL="http"
RED5_HOST="kaltura.local"
USER_CONSENT="0"
CONFIG_CHOICE="0"
IS_SSL="N"' > kaltura.ans
/opt/kaltura/bin/kaltura-config-all.sh kaltura.ans
unzip oflaDemo-r4472-java6.war -d/usr/lib/red5/webapps/oflaDemo
service red5 restart
/opt/kaltura/bin/kaltura-red5-config.sh

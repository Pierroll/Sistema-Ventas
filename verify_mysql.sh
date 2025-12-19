#!/bin/bash

echo "===== VERIFICAR ORIGEN DE MYSQL ====="
echo ""

echo "1. ¿Dónde está el binario mysql?"
which mysql
echo ""

echo "2. ¿Dónde está el binario mysqld?"
which mysqld
echo ""

echo "3. ¿Versión de MySQL?"
mysql --version
echo ""

echo "4. ¿Procesos MySQL corriendo?"
ps aux | grep mysql | grep -v grep
echo ""

echo "5. ¿Socket de MySQL en XAMPP?"
ls -la /Applications/XAMPP/xamppfiles/var/mysql/mysql.sock 2>/dev/null || echo "   No existe socket de XAMPP"
echo ""

echo "6. ¿Socket de MySQL local?"
ls -la /tmp/mysql.sock 2>/dev/null || echo "   No existe /tmp/mysql.sock"
ls -la /var/run/mysqld/mysql.sock 2>/dev/null || echo "   No existe /var/run/mysqld/mysql.sock"
echo ""

echo "7. ¿Es Homebrew MySQL?"
brew list mysql 2>/dev/null && echo "   ✓ MySQL instalado via Homebrew" || echo "   ✗ No es Homebrew"
echo ""

echo "8. ¿Configuración de MySQL?"
mysql -u root -e "SELECT @@version, @@socket, @@datadir;" 2>/dev/null || echo "   (No se puede conectar)"
echo ""
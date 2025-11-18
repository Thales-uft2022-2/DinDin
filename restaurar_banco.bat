@echo off
echo Restaurando o banco de dados do Thales...
C:\xampp\mysql\bin\mysql.exe -u root dindin < backup_thales.sql
echo.
echo Pronto! O banco foi restaurado com sucesso.
echo Pode fechar esta janela.
pause
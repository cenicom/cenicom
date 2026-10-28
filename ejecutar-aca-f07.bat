@echo off
cd /d C:\xampp1\htdocs\cenicom

if not exist storage\logs\aca-year02 mkdir storage\logs\aca-year02

echo ==========================================
echo ACA-F07 - CONCURRENCIA DE PERIODOS
echo ==========================================
echo.

C:\xampp1\php\php.exe vendor\bin\phpunit --configuration=phpunit.aca-year02.xml tests\Feature\Modules\Academic\AcademicPeriodConcurrencyTest.php > storage\logs\aca-year02\F07.txt 2>&1

set F07=%ERRORLEVEL%

type storage\logs\aca-year02\F07.txt

echo.
echo ==========================================
echo CODIGO DE SALIDA: %F07%
echo ==========================================

echo F07=%F07% > storage\logs\aca-year02\resumen.txt

echo.
echo Resultados guardados en:
echo C:\xampp1\htdocs\cenicom\storage\logs\aca-year02\F07.txt
echo.
pause
exit /b %F07%

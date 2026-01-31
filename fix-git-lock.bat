@echo off
echo ========================================
echo  Heqja e index.lock dhe riparimi i Git
echo ========================================
echo.
echo MBYLL Cursor/VS Code para se te vazhdosh!
echo Shtyp ndonje tast per te vazhduar...
pause >nul

set GITDIR=%~dp0.git
set LOCK=%GITDIR%index.lock

if exist "%LOCK%" (
    del /f /q "%LOCK%" 2>nul
    if exist "%LOCK%" (
        echo [GABIM] Nuk mund te fshihet index.lock.
        echo Provo: mbylle OneDrive (kliko djathtas ikonen - Quit), pastaj ekzekuto perseri kete skedar.
        pause
        exit /b 1
    )
    echo [OK] index.lock u fshi.
) else (
    echo [OK] index.lock nuk ekziston.
)

cd /d "%~dp0"
git rebase --abort 2>nul
echo.
echo Tani mund te ekzekutosh:
echo   git add .
echo   git commit -m "Faza 2"
echo   git pull origin DionBranch
echo   git push origin DionBranch
echo.
pause

echo off
cls
rem SET path=C:\Documents and Settings\XFR4206003\Bureau\Personnel - Basile Parent\wamp\www\lecteur\ressources
SET path=C:\Users\Basile\Desktop\lecteur_save\ressources
SET input_path=%path%\video_traitees
SET output_path=%path%\thumbnails
for %%f in ("%input_path%\*.webm") do call :create_thumbnail "%%f" "%%~nxf"
pause

:create_thumbnail
echo Traitement du fichier %~2
rem ffmpeg.exe  -itsoffset -16  -i %1 -vcodec mjpeg -vframes 3 -an -f rawvideo -s 320x240 "%output_path%\%~2.jpg" -y
ffmpeg-64bits.exe -ss 00:15  -i %1 -vcodec mjpeg -vframes 3 -an -f rawvideo -s 240x160 "%output_path%\%~2.jpg" -y
:fin_create_thumbnail

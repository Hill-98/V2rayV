using System;
using System.Diagnostics;
using System.IO;
using System.Security.Cryptography;
using System.Windows.Forms;

namespace Launcher
{
    internal static class Php
    {

        public static void Init()
        {
            if (!CheckPhpVersion())
            {
                Application.Run(new PhpDownload());
            }
            UpdatePhpIni();
        }

        private static bool CheckPhpVersion()
        {
            var result = false;
            try
            {
                var phpProcess = new Process()
                {
                    StartInfo =
                    {
                        FileName = Program.PhpBin,
                        Arguments = "-r \"echo PHP_VERSION;\"",
                        UseShellExecute = false,
                        CreateNoWindow = true,
                        RedirectStandardOutput = true
                    }
                };
                phpProcess.OutputDataReceived += (sender, e) =>
                {
                    if (e.Data != null)
                    {
                        var phpVersion = new Version(e.Data);
                        if (phpVersion.Major >= 7 && phpVersion.Minor >= 2)
                        {
                            result = true;
                        }
                    }
                };
                phpProcess.Start();
                phpProcess.BeginOutputReadLine();
                phpProcess.WaitForExit();
            }
            catch (Exception)
            {
                // ignored
            }

            return result;
        }


        private static void UpdatePhpIni()
        {
            DiffFileAndUpdate(Program.BasePath + "_php\\php.ini", Program.PhpPath + "php.ini");
            DiffFileAndUpdate(Program.BasePath + "_php\\cacert.pem", Program.PhpPath + "cacert.pem");
        }

        private static void DiffFileAndUpdate(string sourceFile, string destFile)
        {
            if (File.Exists(sourceFile))
            {
                try
                {
                    var stream = new FileStream(sourceFile, FileMode.Open);
                    var hash = MD5.Create().ComputeHash(stream);
                    var sourceHash = BitConverter.ToString(hash).Replace("-", "");
                    stream.Close();
                    stream = new FileStream(destFile, FileMode.Open);
                    hash = MD5.Create().ComputeHash(stream);
                    var destHash = BitConverter.ToString(hash).Replace("-", "");
                    stream.Close();
                    if (sourceHash == destHash)
                    {
                        return;
                    }
                }
                catch (Exception)
                {
                    // ignored
                }
            }

            try
            {
                File.Copy(sourceFile, destFile, true);
            }
            catch (Exception e)
            {
                const string msg = "PHP Runtime config file copy fail.\nYou can manually copy the file {0} to {1}\nException: {2}";
                MessageBox.Show(string.Format(msg, sourceFile, destFile, e.Message),
                    Application.ProductName + @" - PHP Runtime Config File", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }
    }
}

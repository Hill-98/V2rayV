using System;
using System.ComponentModel;
using System.Diagnostics;
using System.IO;
using System.Net;
using System.Security.Cryptography;
using System.Windows.Forms;

namespace Launcher
{
    class Php
    {

        public Php()
        {
            if (!CheckPhpVersion())
            {
                Application.Run(new PhpDownload());
            }
            UpdatePhpIni();
        }

        private bool CheckPhpVersion()
        {
            var result = false;
            try
            {
                var PhpProcess = new Process()
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
                PhpProcess.OutputDataReceived += (sender, e) =>
                {
                    if (e.Data != null)
                    {
                        var PhpVersion = new Version(e.Data);
                        if (PhpVersion.Major >= 7 && PhpVersion.Minor >= 2)
                        {
                            result = true;
                        }
                    }
                };
                PhpProcess.Start();
                PhpProcess.BeginOutputReadLine();
                PhpProcess.WaitForExit();
            }
            catch (Exception)
            {
            }
            return result;
        }



        public void UpdatePhpIni()
        {
            DiffFileAndUpdate(Program.BasePath + "_php\\php.ini", Program.PhpPath + "php.ini");
            DiffFileAndUpdate(Program.BasePath + "_php\\cacert.pem", Program.PhpPath + "cacert.pem");
        }

        private void DiffFileAndUpdate(string sourceFile, string destFile)
        {
            if (File.Exists(sourceFile))
            {
                try
                {
                    var stream = new FileStream(sourceFile, FileMode.Open);
                    var hash = MD5.Create().ComputeHash(stream);
                    string sourceHash = BitConverter.ToString(hash).Replace("-", "");
                    stream.Close();
                    stream = new FileStream(destFile, FileMode.Open);
                    hash = MD5.Create().ComputeHash(stream);
                    string destHash = BitConverter.ToString(hash).Replace("-", "");
                    stream.Close();
                    if (sourceHash == destHash)
                    {
                        return;
                    }
                }
                catch (Exception)
                {
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
                    Application.ProductName + " - PHP Runtime Config File", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }
    }
}

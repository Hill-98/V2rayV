using System;
using System.ComponentModel;
using System.IO;
using System.IO.Compression;
using System.Net;
using System.Security.Cryptography;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Launcher
{
    class Php
    {
        string phpPath;

        public Php(string phpPath)
        {
            this.phpPath = phpPath;
        }

        public async Task<bool> Download()
        {
            // 初始化下载客户端
            WebClient client = new WebClient();
            client.Headers.Add("User-Agent", "Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko");
            client.DownloadFileCompleted += Client_DownloadFileCompleted;
            client.DownloadProgressChanged += Client_DownloadProgressChanged;
            string downloadUrl = "https://windows.php.net/downloads/releases/latest/php-7.3-nts-Win32-VC15-{0}-latest.zip";
            if (Environment.Is64BitOperatingSystem)
            {
                downloadUrl = string.Format(downloadUrl, "x64");
            } else
            {
                downloadUrl = string.Format(downloadUrl, "x86");
            }
            Program.notifyIcon.ShowBalloonTip(3000, Application.ProductName, "Downloading PHP runtime...", ToolTipIcon.Info);
            try
            {
                string fileName = Environment.GetEnvironmentVariable("temp") + "\\vvv-php.zip";
                File.Delete(fileName);
                // 下载文件
                await client.DownloadFileTaskAsync(new Uri(downloadUrl), fileName);
                if (!Directory.Exists(phpPath))
                {
                    Directory.CreateDirectory(phpPath);
                }
                ZipArchive zip = ZipFile.OpenRead(fileName);
                // 循环 ZIP 存档文件
                foreach (ZipArchiveEntry entry in zip.Entries)
                {
                    string path = Path.Combine(phpPath, entry.FullName);
                    // 判断目标是否是目录
                    if (path.EndsWith("/"))
                    {
                        if (!Directory.Exists(Path.Combine(phpPath, entry.FullName)))
                        {
                            Directory.CreateDirectory(path);
                        }
                    }
                    else
                    {
                        entry.ExtractToFile(path, true);
                    }
                }
                zip.Dispose();
                return true;
            }
            catch (Exception e)
            {
                MessageBox.Show(e.Message, Application.ProductName + " - PHP Runtime", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return false;
            }
        }

        public void UpdatePhpIni()
        {
            DiffFileAndUpdate(Program.basePath + "_php\\php.ini", phpPath + "\\php.ini");
            DiffFileAndUpdate(Program.basePath + "_php\\cacert.pem", phpPath + "\\cacert.pem");
        }

        private void DiffFileAndUpdate(string sourceFile, string destFile)
        {
            FileStream stream;
            byte[] hash;
            if (File.Exists(sourceFile))
            {
                try
                {
                    stream = new FileStream(sourceFile, FileMode.Open);
                    hash = MD5.Create().ComputeHash(stream);
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
            File.Copy(sourceFile, destFile);
        }

        private void Client_DownloadProgressChanged(object sender, DownloadProgressChangedEventArgs e)
        {
            string text = string.Format("Downloading PHP Runtime\n{0} MB / {1} MB", (e.BytesReceived / 1024 / 1024).ToString("0.00"), (e.TotalBytesToReceive / 1024 / 1024).ToString("0.00"));
            Program.notifyIcon.Text = text;
        }

        private void Client_DownloadFileCompleted(object sender, AsyncCompletedEventArgs e)
        {
            if (e.Error == null)
            {
                Program.notifyIcon.ShowBalloonTip(3000, Application.ProductName, "PHP runtime download completed", ToolTipIcon.Info);

            } else
            {
                Program.notifyIcon.ShowBalloonTip(3000, Application.ProductName, "PHP runtime download error", ToolTipIcon.Error);
            }
        }
    }
}

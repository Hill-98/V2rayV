using System;
using System.ComponentModel;
using System.IO;
using System.IO.Compression;
using System.Net;
using System.Windows.Forms;

namespace Launcher
{
    public partial class PhpDownload : Form
    {
        private bool complete = false;
        public PhpDownload()
        {
            InitializeComponent();
        }

        public async void DownloadPhp()
        {
            // 初始化下载客户端
            var client = new WebClient();
            client.Headers.Add("User-Agent", "Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko");
            client.DownloadFileCompleted += Client_DownloadFileCompleted;
            client.DownloadProgressChanged += Client_DownloadProgressChanged;
            const string downloadUrl = "https://windows.php.net/downloads/releases/latest/php-7.3-nts-Win32-VC15-{0}-latest.zip";
            try
            {
                var fileName = Path.GetTempFileName();
                // 下载文件
                await client.DownloadFileTaskAsync(new Uri(string.Format(downloadUrl, Environment.Is64BitOperatingSystem ? "x64" : "x86")), fileName);
                if (!Directory.Exists(Program.PhpPath))
                {
                    Directory.CreateDirectory(Program.PhpPath);
                }
                var zip = ZipFile.OpenRead(fileName);
                // 循环 ZIP 存档文件
                foreach (var entry in zip.Entries)
                {
                    var path = Path.Combine(Program.PhpPath, entry.FullName);
                    // 判断目标是否是目录
                    if (path.EndsWith("/"))
                    {
                        if (!Directory.Exists(Path.Combine(Program.PhpPath, entry.FullName)))
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
                File.Delete(fileName);
                complete = true;
                Close();
            }
            catch (Exception e)
            {
                MessageBox.Show(e.Message, Application.ProductName + " - PHP Runtime", MessageBoxButtons.OK,
                    MessageBoxIcon.Error);
            }
        }

        private void Client_DownloadProgressChanged(object sender, DownloadProgressChangedEventArgs e)
        {
            progressBar1.Value = e.ProgressPercentage;
        }

        private void Client_DownloadFileCompleted(object sender, AsyncCompletedEventArgs e)
        {
            if (e.Error != null)
            {
                MessageBox.Show("Download failed.", Application.ProductName + " - PHP Runtime", MessageBoxButtons.OK,
                    MessageBoxIcon.Error);
                Application.Exit();
            }
        }

        private void PhpDownload_Load(object sender, EventArgs e)
        {
            DownloadPhp();
        }

        private void PhpDownload_FormClosed(object sender, FormClosedEventArgs e)
        {
            if (!complete)
            {
                Application.Exit();
            }
        }
    }
}


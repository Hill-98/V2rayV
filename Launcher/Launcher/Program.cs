using System;
using System.Diagnostics;
using System.Threading.Tasks;
using System.IO;
using System.Windows.Forms;
using System.Management;
using System.Security.Cryptography;
using System.Threading;

namespace Launcher
{
    static class Program
    {
#if DEBUG
        public static string basePath = AppDomain.CurrentDomain.BaseDirectory + "..\\..\\..\\..\\";
#else
        public static string basePath = AppDomain.CurrentDomain.BaseDirectory;
#endif
        private static readonly string wathPath = basePath + "storage\\app\\";
        public static NotifyIcon notifyIcon;
        static Process V2rayV_Process;
        static Process V2rayV_Queue_Process;
        static void Main(string[] args)
        {
            // 禁止重复运行
            bool isRuned;
            Mutex mutex = new Mutex(true, "OnlyRunOneInstance", out isRuned);
            if (!isRuned)
            {
                OpenWebUI();
                return;
            }
            //Application.EnableVisualStyles();
            //Application.SetCompatibleTextRenderingDefault(false);

            // 文件监控服务
            try
            {
                FileSystemWatcher fileWatch = new FileSystemWatcher(wathPath, "*.vvv");
                fileWatch.Changed += FileWatch_Changed;
                fileWatch.EnableRaisingEvents = true;
            }
            catch (Exception e)
            {
                MessageBox.Show(e.Message, Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }
            // 托盘上下文菜单
            ContextMenu contextMenu = new ContextMenu();
            contextMenu.MenuItems.Add(new MenuItem(Application.ProductName, Icon_DoubleClick));
            contextMenu.MenuItems.Add(new MenuItem("Exit", delegate (object Sender, EventArgs e)
            {
                Exit();
            }));
            // 托盘图标
            notifyIcon = new NotifyIcon
            {
                Icon = new System.Drawing.Icon(basePath + "public\\favicon.ico"),
                Text = Application.ProductName,
                Visible = true,
                ContextMenu = contextMenu
            };
            notifyIcon.DoubleClick += Icon_DoubleClick;
            if (Start(args))
            {
                Application.Run();
            } else
            {
                Exit();
            }
        }

        private static void FileWatch_Changed(object sender, FileSystemEventArgs e)
        {
            if (e.ChangeType == WatcherChangeTypes.Deleted)
            {
                return;
            }
            FileStream file = null;
            StreamReader reader;
            try
            {
                file = new FileStream(e.FullPath, FileMode.Open, FileAccess.Read, FileShare.Read);
                reader = new StreamReader(file);
            }
            catch (Exception)
            {
                if (file != null)
                {
                    file.Close();
                }
                return;
            }
            if (file.Length >= 10)
            {
                file.Close();
                return;
            }
            string context = reader.ReadToEnd();
            switch (e.Name)
            {
                case "v2ray.vvv":
                    setV2ray(context);
                    break;
                case "boot.vvv":
                    setBoot(context);
                    break;
            }
            file.Close();
        }

        private static bool Start(string[] args)
        {
            string context;
            try
            {
                if (File.Exists(wathPath + "boot.vvv"))
                {
                    context = File.ReadAllText(wathPath + "boot.vvv");
                    setBoot(context);
                }
                if (File.Exists(wathPath + "v2ray.vvv"))
                {
                    context = File.ReadAllText(wathPath + "v2ray.vvv");
                    setV2ray(context);
                }
            }
            catch (Exception)
            {
            }

            string artisan = basePath + "artisan";
#if DEBUG
            string phpBin = @"D:\laragon\bin\php\php7\php.exe";
#else
            string phpBin = basePath + "php\\php.exe";
            Php php = new Php(Path.GetDirectoryName(phpBin));
            if (!File.Exists(phpBin))
            {
                Task<bool> phpDownload = Task.Run(() => php.Download());
                phpDownload.Wait();
                if (phpDownload.Result)
                {
                    notifyIcon.Text = Application.ProductName;
                }
                else
                {
                    Thread.Sleep(3000);
                    return false;
                }
            }
            php.UpdatePhpIni();
            V2rayV v2rayv = new V2rayV();
            int check_status = v2rayv.CheckEnvFile();
            if (check_status == -1)
            {
                MessageBox.Show("V2rayV initialization failed,", Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                return false;
            }
            else if (check_status != 0)
            {
                Process.Start(phpBin, artisan + " config:cache");
            }
#endif
            V2rayV_Process = new Process()
            {
                StartInfo =
                {
                    FileName = phpBin,
                    Arguments = artisan + " vvv:start",
                    WorkingDirectory = basePath,
                    UseShellExecute = false,
                    CreateNoWindow = true,
                    RedirectStandardOutput = true
                }
            };
            V2rayV_Queue_Process = new Process()
            {
                StartInfo =
                {
                    FileName = phpBin,
                    Arguments = artisan + " queue:work --queue=high,default",
                    WorkingDirectory = basePath,
                    UseShellExecute = false,
                    CreateNoWindow = true,
                }
            };
            V2rayV_Process.OutputDataReceived += new DataReceivedEventHandler((sender, e) =>
            {
                if (e.Data == "Ready")
                {
                    try
                    {
                        V2rayV_Queue_Process.Start();
                        V2rayV_Process.CancelOutputRead();
                    }
                    catch (Exception ex)
                    {
                        MessageBox.Show("V2rayV Queue failed to start.\nException: " + ex.Message, Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                        Exit();
                    }
                    
                }
            });
#if !DEBUG
            try
            {
                V2rayV_Process.Start();
                V2rayV_Process.BeginOutputReadLine();
            }
            catch (Exception e)
            {
                MessageBox.Show("V2rayV failed to start.\nException: " + e.Message, Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                return false;
            }
#endif
#if !DEBUG
            if (args.Length == 0 || args[0] != "autorun")
            {
                OpenWebUI();
            }
#endif
            return true;
        }

        private static void setBoot(string context)
        {
            bool boot = false;
            try
            {
                boot = Convert.ToBoolean(context);
            }
            catch (Exception)
            {
            }
            new AutoStart(boot);
        }

        private static void setV2ray(string context)
        {
            int code = 0;
            try
            {
                code = Convert.ToInt32(context);
            }
            catch (Exception)
            {
            }
            V2ray.Control(code);
        }

        private static void OpenWebUI()
        {
            Process.Start("http://localhost:8246");
        }

        private static void Icon_DoubleClick(object sender, EventArgs e)
        {
            OpenWebUI();
        }
        private static void Exit()
        {
            notifyIcon.Visible = false;
            V2ray.Control(V2ray.STOP);
            try
            {
                KillProcessAndChildren(V2rayV_Process.Id);
            } catch (Exception)
            {
            }
            try
            {
                KillProcessAndChildren(V2rayV_Queue_Process.Id);
            }
            catch (Exception)
            {
            }
            Application.Exit();
        }

        /// <summary>
        /// Kill a process, and all of its children, grandchildren, etc.
        /// </summary>
        /// <param name="pid">Process ID.</param>
        private static void KillProcessAndChildren(int pid)
        {
            if (pid == 0)
            {
                return;
            }
            ManagementObjectSearcher searcher = new ManagementObjectSearcher
                    ("Select * From Win32_Process Where ParentProcessID=" + pid);
            ManagementObjectCollection moc = searcher.Get();
            foreach (ManagementObject mo in moc)
            {
                KillProcessAndChildren(Convert.ToInt32(mo["ProcessID"]));
            }
            try
            {
                Process proc = Process.GetProcessById(pid);
                proc.Kill();
            }
            catch (ArgumentException)
            {
                // Process already exited.
            }
        }
    }
}

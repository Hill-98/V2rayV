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
    internal static class Program
    {
#if DEBUG
        public static readonly string BasePath = AppDomain.CurrentDomain.BaseDirectory + @"..\..\..\..\";
#else
        public static readonly string BasePath = AppDomain.CurrentDomain.BaseDirectory;
#endif
        private static readonly string WatchPath = BasePath + @"storage\app\";
        public static readonly string PhpPath = BasePath + @"php\";
        public static readonly string PhpBin = PhpPath + "php.exe";
        public static NotifyIcon notifyIcon;
        private static Process V2rayV_Process;
        private static Process V2rayV_Queue_Process;
        private static FileWatch FileWatch;
        

        private static void Main(string[] args)
        {
            // 禁止重复运行
            _ = new Mutex(true, "OnlyRunOneInstance", out var isRun);
            if (!isRun)
            {
                OpenWebUI();
                return;
            }
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            // Php 环境初始化
            new Php();
            // 文件监控服务
            try
            {
                FileWatch = new FileWatch(WatchPath, "*.vvv");
            }
            catch (Exception e)
            {
                MessageBox.Show(e.Message, Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            // 托盘上下文菜单
            var contextMenu = new ContextMenu();
            contextMenu.MenuItems.Add(new MenuItem(Application.ProductName, Icon_DoubleClick));
            contextMenu.MenuItems.Add(new MenuItem("Exit", delegate { Exit(); }));
            // 托盘图标
            notifyIcon = new NotifyIcon
            {
                Icon = new System.Drawing.Icon(BasePath + @"public\favicon.ico"),
                Text = Application.ProductName,
                Visible = true,
                ContextMenu = contextMenu
            };
            notifyIcon.DoubleClick += Icon_DoubleClick;
            Start(args);
            Application.Run();
//            if (Start(args))
//            {
//
//            }
//            else
//            {
//                Exit();
//            }
        }

        private static bool Start(string[] args)
        {
            try
            {
                string context;
                if (File.Exists(WatchPath + "boot.vvv"))
                {
                    context = File.ReadAllText(WatchPath + "boot.vvv");
                    FileWatch.BootFile(context);
                }

                if (File.Exists(WatchPath + "v2ray.vvv"))
                {
                    context = File.ReadAllText(WatchPath + "v2ray.vvv");
                    FileWatch.V2RayFile(context);
                }
            }
            catch (Exception)
            {
            }

            var artisan = BasePath + "artisan";
            var v2rayV = new V2rayV();
            var check_status = v2rayV.CheckEnvFile();
            if (check_status == -1)
            {
                MessageBox.Show("V2rayV initialization failed,", Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                return false;
            }
            if (check_status != 0)
            {
                Process.Start(PhpBin, artisan + " config:cache");
            }
            V2rayV_Process = new Process()
            {
                StartInfo =
                {
                    FileName = PhpBin,
                    Arguments = artisan + " vvv:start",
                    WorkingDirectory = BasePath,
                    UseShellExecute = false,
                    CreateNoWindow = true,
                    RedirectStandardOutput = true
                }
            };
            V2rayV_Queue_Process = new Process()
            {
                StartInfo =
                {
                    FileName = PhpBin,
                    Arguments = artisan + " queue:work --queue=high,default",
                    WorkingDirectory = BasePath,
                    UseShellExecute = false,
                    CreateNoWindow = true,
                }
            };
            V2rayV_Process.OutputDataReceived += (sender, e) =>
            {
                if (e.Data != "Ready") return;
                try
                {
                    V2rayV_Queue_Process.Start();
                    V2rayV_Process.CancelOutputRead();
                }
                catch (Exception ex)
                {
                    MessageBox.Show("V2rayV Queue failed to start.\nException: " + ex.Message,
                        Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                    Exit();
                }
            };
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
            if (args.Length == 0 || args[0] != "autorun")
            {
                OpenWebUI();
            }
#endif
            return true;
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
            }
            catch (Exception)
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

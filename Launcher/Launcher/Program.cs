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
        public static NotifyIcon NotifyIcon;
        private static Process _v2RayVProcess;
        private static Process _v2RayVQueueProcess;


        private static void Main(string[] args)
        {
            // 禁止重复运行
            _ = new Mutex(true, "OnlyRunOneInstance", out var isRun);
            if (!isRun)
            {
                OpenWebUi();
                return;
            }
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            // Php 环境初始化
            Php.Init();
            // 文件监控服务
            try
            {
                FileWatch.Init(WatchPath, "*.vvv");
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
            NotifyIcon = new NotifyIcon
            {
                Icon = new System.Drawing.Icon(BasePath + @"public\favicon.ico"),
                Text = Application.ProductName,
                Visible = true,
                ContextMenu = contextMenu
            };
            NotifyIcon.DoubleClick += Icon_DoubleClick;
            try
            {
                Start(args);
                Application.Run();
            }
            catch (Exception)
            {
                // ignored
            }
        }

        private static void Start(string[] args)
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
                // ignored
            }

            var artisan = BasePath + "artisan";
            var checkStatus = V2RayV.CheckEnvFile();
            if (checkStatus == -1)
            {
                MessageBox.Show("V2rayV initialization failed,", Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }
            if (checkStatus != 0)
            {
                Process.Start(PhpBin, artisan + " config:cache");
            }
            _v2RayVProcess = new Process()
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
            _v2RayVQueueProcess = new Process()
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
            _v2RayVProcess.OutputDataReceived += (sender, e) =>
            {
                if (e.Data != "Ready") return;
                try
                {
                    _v2RayVQueueProcess.Start();
                    _v2RayVProcess.CancelOutputRead();
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
                _v2RayVProcess.Start();
                _v2RayVProcess.BeginOutputReadLine();
            }
            catch (Exception e)
            {
                MessageBox.Show("V2rayV failed to start.\nException: " + e.Message, Application.ProductName, MessageBoxButtons.OK, MessageBoxIcon.Error);
                throw;
            }
            if (args.Length == 0 || args[0] != "/autorun")
            {
                OpenWebUi();
            }
#endif
        }

        private static void OpenWebUi()
        {
            Process.Start("http://localhost:8246");
        }

        private static void Icon_DoubleClick(object sender, EventArgs e)
        {
            OpenWebUi();
        }

        private static void Exit()
        {
            NotifyIcon.Visible = false;
            V2Ray.Control(V2Ray.STOP);
            try
            {
                KillProcessAndChildren(_v2RayVProcess.Id);
            }
            catch (Exception)
            {
                // ignored
            }

            try
            {
                KillProcessAndChildren(_v2RayVQueueProcess.Id);
            }
            catch (Exception)
            {
                // ignored
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

            var searcher = new ManagementObjectSearcher
                ("Select * From Win32_Process Where ParentProcessID=" + pid);
            var moc = searcher.Get();
            foreach (var o in moc)
            {
                var mo = (ManagementObject) o;
                KillProcessAndChildren(Convert.ToInt32(mo["ProcessID"]));
            }

            try
            {
                var proc = Process.GetProcessById(pid);
                proc.Kill();
            }
            catch (ArgumentException)
            {
                // Process already exited.
            }
        }
    }
}

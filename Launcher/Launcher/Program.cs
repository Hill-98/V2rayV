using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Threading.Tasks;
using System.IO;
using System.Windows.Forms;
using System.Management;

namespace Launcher
{
    static class Program
    {
#if DEBUG
        public static string basePath = AppDomain.CurrentDomain.BaseDirectory + "..\\..\\..\\..\\";
#else
        public static string basePath = AppDomain.CurrentDomain.BaseDirectory;
#endif
        private static string wathPath = basePath + "storage\\app\\";
        static NotifyIcon notifyIcon;
        static Process V2rayV;
        static Process V2rayV_Queue;
        static void Main(string[] args)
        {
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
            Start(args);
            Application.Run();
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

        private static void Start(string[] args)
        {
            string context;
            context = File.ReadAllText(wathPath + "boot.vvv");
            setBoot(context);
            context = File.ReadAllText(wathPath + "v2ray.vvv");
            setV2ray(context);
            string artisan = basePath + "artisan";
#if DEBUG
            string PhpBin = @"D:\laragon\bin\php\php7\php.exe";
#else
            string PhpBin = basePath + "php\\php.exe";
#endif
            string EnvFile = basePath + ".env";
            if (!File.Exists(EnvFile))
            {
                File.Copy(EnvFile + ".vvv", EnvFile);
                Process.Start(PhpBin, artisan + " key:generate");
            }
            V2rayV = new Process()
            {
                StartInfo =
                {
                    FileName = PhpBin,
                    Arguments = artisan + " vvv:start",
                    WorkingDirectory = basePath,
                    UseShellExecute = false,
                    CreateNoWindow = true,
                    RedirectStandardOutput = true
                }
            };
            V2rayV_Queue = new Process()
            {
                StartInfo =
                {
                    FileName = @"D:\laragon\bin\php\php7\php.exe",
                    Arguments = artisan + " queue:work --queue=high,default",
                    WorkingDirectory = basePath,
                    UseShellExecute = false,
                    CreateNoWindow = true,
                }
            };
            V2rayV.OutputDataReceived += new DataReceivedEventHandler((sender, e) =>
            {
                if (e.Data == "Ready")
                {
                    V2rayV_Queue.Start();
                    V2rayV.CancelOutputRead();
                }
            });
            V2rayV.Start();
            V2rayV.BeginOutputReadLine();
#if !DEBUG
            if (args.Length != 0 && args[0] == "autorun")
            {
                return;
            }
            OpenWebUI();
#endif

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
            KillProcessAndChildren(V2rayV.Id);
            KillProcessAndChildren(V2rayV_Queue.Id);
            Application.Exit();
        }

        /// <summary>
        /// Kill a process, and all of its children, grandchildren, etc.
        /// </summary>
        /// <param name="pid">Process ID.</param>
        private static void KillProcessAndChildren(int pid)
        {
            // Cannot close 'system idle process'.
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
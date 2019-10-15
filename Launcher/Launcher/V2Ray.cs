using System.Diagnostics;
using System.IO;
using System.Windows.Forms;

namespace Launcher
{
    internal static class V2Ray
    {
        private const int START = 1;
        public const int STOP = 2;

        private static Process _process;

        public static void Control(int code)
        {
            switch (code)
            {
                case START:
                    Start();
                    break;
                case STOP:
                    Stop();
                    break;
            }
        }

        private static void Start()
        {
            if (_process != null && !_process.HasExited)
            {
                Stop();
            }
            var v2RayBin = Program.BasePath + @"storage\app\v2ray\wv2ray.exe";
            if (File.Exists(v2RayBin))
            {
                _process = new Process()
                {
                    StartInfo =
                {
                    FileName = v2RayBin,
                }
                };
                _process.Start();
            } else
            {
                Program.NotifyIcon.ShowBalloonTip(3000, Application.ProductName, "V2ray Core not found.", ToolTipIcon.Info);
            }
        }

        private static void Stop()
        {
            if (_process == null || _process.HasExited) return;
            _process.Kill();
            _process = null;
        }
    }
}

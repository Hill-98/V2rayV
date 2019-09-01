using System.Diagnostics;
using System.IO;

namespace Launcher
{
    static internal class V2ray
    {
        public const int START = 1;
        public const int STOP = 2;

        static Process process;

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
            if (process != null && !process.HasExited)
            {
                Stop();
            }
            string V2rayBin = Program.basePath + @"storage\app\v2ray\wv2ray.exe";
            if (File.Exists(V2rayBin))
            {
                process = new Process()
                {
                    StartInfo =
                {
                    FileName = V2rayBin,
                }
                };
                process.Start();
            }
        }

        private static void Stop()
        {
            if (process != null && !process.HasExited)
            {
                process.Kill();
                process = null;
            }
        }
    }
}

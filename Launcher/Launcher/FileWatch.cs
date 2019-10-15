using System;
using System.IO;

namespace Launcher
{
    internal static class FileWatch
    {
        public static void Init(string path, string filter)
        {
            new FileSystemWatcher(path, filter)
            {
                EnableRaisingEvents = true
            }.Changed += WatcherOnChanged;
        }

        private static void WatcherOnChanged(object sender, FileSystemEventArgs e)
        {
            if (e.ChangeType == WatcherChangeTypes.Deleted || e.ChangeType == WatcherChangeTypes.Renamed)
            {
                return;
            }

            FileStream file;
            StreamReader reader;
            try
            {
                file = new FileStream(e.FullPath, FileMode.Open, FileAccess.Read, FileShare.Read);
                reader = new StreamReader(file);
            }
            catch (Exception)
            {
                return;
            }

            if (file.Length >= 10)
            {
                file.Close();
                return;
            }

            var context = reader.ReadToEnd();
            file.Close();
            switch (e.Name)
            {
                case "boot.vvv":
                    BootFile(context);
                    break;
                case "v2ray.vvv":
                    V2RayFile(context);
                    break;
            }
        }

        public static void BootFile(string context)
        {
            var isBoot = false;
            try
            {
                isBoot = Convert.ToBoolean(context);
            }
            catch (Exception)
            {
                // ignored
            }

            Helper.SetAutoRun(isBoot);
        }

        public static void V2RayFile(string context)
        {
            var code = 0;
            try
            {
                code = Convert.ToInt32(context);
            }
            catch (Exception)
            {
                // ignored
            }

            V2Ray.Control(code);
        }
    }
}

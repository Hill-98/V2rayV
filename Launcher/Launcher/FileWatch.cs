using System;
using System.IO;

namespace Launcher
{
    public class FileWatch
    {
        public FileWatch(string path, string filter)
        {
            try
            {
                var Watcher = new FileSystemWatcher(path, filter)
                {
                    EnableRaisingEvents = true
                };
                Watcher.Changed += WatcherOnChanged;
            }
            catch (Exception e)
            {
                throw e;
            }
        }

        private void WatcherOnChanged(object sender, FileSystemEventArgs e)
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

        public void BootFile(string context)
        {
            var boot = false;
            try
            {
                boot = Convert.ToBoolean(context);
            }
            catch (Exception)
            {
            }

            new AutoStart(boot);
        }

        public void V2RayFile(string context)
        {
            var code = 0;
            try
            {
                code = Convert.ToInt32(context);
            }
            catch (Exception)
            {
            }

            V2ray.Control(code);
        }
    }
}

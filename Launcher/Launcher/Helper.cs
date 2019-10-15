using System;
using Microsoft.Win32;

namespace Launcher
{
    internal static class Helper
    {
        public static void SetAutoRun(bool isBoot)
        {
            try
            {
                var runReg = Registry.CurrentUser.CreateSubKey(@"Software\Microsoft\Windows\CurrentVersion\Run");
                var filename = System.Diagnostics.Process.GetCurrentProcess().MainModule?.FileName;
                const string cmd = "\"{0}\" /autorun";
                if (isBoot)
                {
                    runReg?.SetValue("V2rayV", string.Format(cmd, filename));
                }
                else
                {
                    runReg?.DeleteValue("V2rayV");
                }
            }
            catch (Exception)
            {
                // ignored
            }
        }
    }
}

using Microsoft.Win32;
using System;

namespace Launcher
{
    class AutoStart
    {
        public AutoStart(bool boot)
        {
            try
            {
                RegistryKey runReg = Registry.CurrentUser.CreateSubKey(@"Software\Microsoft\Windows\CurrentVersion\Run");
                string filename = System.Diagnostics.Process.GetCurrentProcess().MainModule.FileName;
                if (boot)
                {
                    runReg.SetValue("V2rayV", filename);
                }
                else
                {
                    runReg.DeleteValue("V2rayV");
                }
            }
            catch (Exception)
            {
            }
        }
    }
}

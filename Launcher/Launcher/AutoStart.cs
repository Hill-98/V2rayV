using Microsoft.Win32;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Launcher
{
    class AutoStart
    {
        public AutoStart(bool boot)
        {
            try
            {
                RegistryKey Run = Registry.CurrentUser.CreateSubKey(@"Software\Microsoft\Windows\CurrentVersion\Run");
                string filename = System.Diagnostics.Process.GetCurrentProcess().MainModule.FileName;
                if (boot)
                {
                    Run.SetValue("V2rayV", filename);
                }
                else
                {
                    Run.DeleteValue("V2rayV");
                }
            }
            catch (Exception)
            {
            }
        }
    }
}

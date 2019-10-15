using System;
using System.IO;
using System.Security.Cryptography;

namespace Launcher
{
    internal static class V2RayV
    {
        /// <summary>
        ///
        /// </summary>
        /// <returns>操作状态码，0 = 无操作，1 = 全新安装，2 = 升级安装，-1 = 失败</returns>
        public static int CheckEnvFile()
        {
            var envFile = Program.BasePath + ".env";
            var envNewHash = "new";
            var envCurrHash = "curr";
            try
            {
                var stream = new FileStream(envFile + ".vvv", FileMode.Open);
                var hash = MD5.Create().ComputeHash(stream);
                envNewHash = BitConverter.ToString(hash).Replace("-", "");
                stream.Close();
            }
            catch (Exception)
            {
                // ignored
            }

            if (File.Exists(envFile + ".md5"))
            {
                try
                {
                    envCurrHash = File.ReadAllText(envFile + ".md5");
                }
                catch (Exception)
                {
                    // ignored
                }
            }

            if (File.Exists(envFile) && envNewHash == envCurrHash) return 0;
            var result = File.Exists(envFile + ".vvv") ? 2 : 1;
            try
            {
                File.Copy(envFile + ".vvv", envFile, true);
            }
            catch (Exception)
            {
                return result == 2 ? 0 : -1;
            }
            File.WriteAllText(envFile + ".md5", envNewHash);
            return result;
        }
    }
}

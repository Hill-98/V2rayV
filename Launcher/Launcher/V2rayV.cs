using System;
using System.IO;
using System.Security.Cryptography;

namespace Launcher
{
    class V2rayV
    {
        /// <summary>
        /// 
        /// </summary>
        /// <returns>操作状态码，0 = 无操作，1 = 全新安装，2 = 升级安装，-1 = 失败</returns>
        public int CheckEnvFile()
        {
            string envFile = Program.basePath + ".env";
            string envNewHash = "new";
            string envCurrHash = "curr";
            try
            {
                FileStream stream = new FileStream(envFile + ".vvv", FileMode.Open);
                byte[] hash = MD5.Create().ComputeHash(stream);
                envNewHash = BitConverter.ToString(hash).Replace("-", "");
                stream.Close();
            }
            catch (Exception)
            {
            }
            if (File.Exists(envFile + ".md5"))
            {
                try
                {
                    envCurrHash = File.ReadAllText(envFile + ".md5");
                }
                catch (Exception)
                {
                }
            }
            if (!File.Exists(envFile) || envNewHash != envCurrHash)
            {
                int result = File.Exists(envFile + ".vvv") ? 2 : 1;
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
            return 0;
        }
    }
}

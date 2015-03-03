using System;
using System.Text;
using System.Reflection;
using System.Security.Principal;
using System.Security.Cryptography;

namespace LiteHTTP.Classes
{
    class Misc
    {
        public static string hash(string input)
        {
            MD5CryptoServiceProvider md5 = new MD5CryptoServiceProvider();
            byte[] temp = md5.ComputeHash(Encoding.UTF8.GetBytes(input));
            StringBuilder sb = new StringBuilder();
            for (int i = 0; i < temp.Length - 1; i++)
            {
                sb.Append(temp[i].ToString("x2"));
            }
            return sb.ToString().ToUpper();
        }

        public static string getLocation()
        {
            string res = Assembly.GetExecutingAssembly().Location;
            if (res == "" || res == null)
            {
                res = Assembly.GetEntryAssembly().Location;
            }
            return res;
        }

        public static bool isAdmin()
        {
            WindowsIdentity id = WindowsIdentity.GetCurrent();
            WindowsPrincipal pr = new WindowsPrincipal(id);
            if (pr.IsInRole(WindowsBuiltInRole.Administrator))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        public static string lastReboot()
        {
            string res = null;
            double since = new Microsoft.VisualBasic.Devices.Computer().Clock.TickCount / 1000 / 60;
            if (since > 60)
            {
                since = since / 60;
                if (since > 24)
                {
                    since = since / 24;
                    res = ((int)since).ToString() + " day(s) ago";
                }
                else
                {
                    res = ((int)since).ToString() + " hour(s) ago";
                }
            }
            else
            {
                res = ((int)since).ToString() + " minute(s) ago";
            }
            return res;
        }

        public static bool processTask(string task, string param)
        {
            // temporary "success" return
            return true;
        }
    }
}

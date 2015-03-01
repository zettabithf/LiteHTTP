using System;
using System.Text;
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
    }
}

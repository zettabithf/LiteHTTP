using System;
using System.IO;
using System.Net;
using System.Text;
using System.Security.Cryptography;

namespace LiteHTTP.Classes
{
    class Communication
    {
        // this is just the base call
        // encryption will be added before official release
        public static string makeRequest(string url, string parameters)
        {
            try
            {
                string result = null;
                byte[] param = Encoding.UTF8.GetBytes(parameters);
                WebRequest req = WebRequest.Create(url);
                req.Method = "POST";
                ((HttpWebRequest)req).UserAgent = "E9BC3BD76216AFA560BFB5ACAF5731A3";
                req.ContentType = "application/x-www-form-urlencoded";
                req.ContentLength = param.Length;
                Stream st = req.GetRequestStream();
                st.Write(param, 0, param.Length);
                st.Close();
                st.Dispose();
                WebResponse resp = req.GetResponse();
                StreamReader sr = new StreamReader(resp.GetResponseStream());
                result = sr.ReadToEnd();
                sr.Close();
                sr.Dispose();
                resp.Close();
                return result;
            }
            catch
            {
                return "rqf";
            }
        }

        public static string encrypt(string input)
        {
            try
            {
                string key = Settings.edkey;
                RijndaelManaged rj = new RijndaelManaged();
                rj.Padding = PaddingMode.Zeros;
                rj.Mode = CipherMode.CBC;
                rj.KeySize = 256;
                rj.BlockSize = 256;
                byte[] ky = Encoding.ASCII.GetBytes(key);
                byte[] inp = Encoding.ASCII.GetBytes(input);
                byte[] res;

                ICryptoTransform enc = rj.CreateEncryptor(ky, ky);
                using (MemoryStream ms = new MemoryStream())
                {
                    using (CryptoStream cs = new CryptoStream(ms, enc, CryptoStreamMode.Write))
                    {
                        cs.Write(inp, 0, inp.Length);
                        cs.FlushFinalBlock();
                        cs.Close();
                        cs.Dispose();
                    }
                    res = ms.ToArray();
                    ms.Close();
                    ms.Dispose();
                }
                return Convert.ToBase64String(res).Replace("+", "~");
            }
            catch { return null; }
        }

        public static string decrypt(string input)
        {
            try
            {
                string key = Settings.edkey;
                RijndaelManaged rj = new RijndaelManaged();
                rj.Padding = PaddingMode.Zeros;
                rj.Mode = CipherMode.CBC;
                rj.KeySize = 256;
                rj.BlockSize = 256;
                byte[] ky = Encoding.ASCII.GetBytes(key);
                byte[] inp = Convert.FromBase64String(input);
                byte[] res = new byte[inp.Length];

                ICryptoTransform dec = rj.CreateDecryptor(ky, ky);
                using (MemoryStream ms = new MemoryStream(inp))
                {
                    using (CryptoStream cs = new CryptoStream(ms, dec, CryptoStreamMode.Read))
                    {
                        cs.Read(res, 0, res.Length);
                        cs.Close();
                        cs.Dispose();
                    }
                    ms.Close();
                    ms.Dispose();
                }
                return Encoding.UTF8.GetString(res).Trim().Replace("\0", "");
            }
            catch (Exception ex) { return ex.Message; }
        }
    }
}

using System;
using System.IO;
using System.Net;
using System.Text;

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
    }
}

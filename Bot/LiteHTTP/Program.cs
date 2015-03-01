using System;
using System.Collections.Generic;
using System.Text;
using LiteHTTP.Classes;

namespace LiteHTTP
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine(Identification.getHardwareID());
            Console.ReadKey();
        }
    }
}

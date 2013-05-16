import java.io.IOException; 
import java.io.PrintWriter; 
import java.io.*; 
import java.net.*; 
 
import javax.servlet.ServletException; 
import javax.servlet.http.HttpServlet; 
import javax.servlet.http.HttpServletRequest; 
import javax.servlet.http.HttpServletResponse; 
import net.sf.json.xml.*; 
import net.sf.json.*; 

import org.apache.commons.lang.StringEscapeUtils;
/** 
 * Servlet implementation class HelloWorld 
 */ 
public class HelloWorld extends HttpServlet { 
	private static final long serialVersionUID = 1L; 
        
    /** 
     * @see HttpServlet#HttpServlet() 
     */ 
    public HelloWorld() { 
        super(); 
        // TODO Auto-generated constructor stub 
    } 
 
	/** 
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse response) 
	 */ 
	public void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException { 
		// TODO Auto-generated method stub 
 
		response.setContentType("text;charset=UTF-8"); 
		PrintWriter out = response.getWriter(); 
		String decodeURL = URLDecoder.decode(request.getQueryString(), "UTF-8");
		//out.println(decodeURL);
		String[] params = decodeURL.split("&");
		String[] p1 = params[0].split("=");
		String[] p2 = params[1].split("=");

		//out.println(p1[1]+" "+p2[1]);
		String urlS = "http://cs-server.usc.edu:26798/discograph_xml_new.php?sQuery="+URLEncoder.encode(p1[1], "UTF-8")+"&qT="+URLEncoder.encode(p2[1], "UTF-8");

		//out.println(request.getParameter("sQuery")+" "+request.getParameter("qT"));
		//String urlS = "http://localhost/webtech/discograph.php?sQuery="+request.getParameter("sQuery")+"&qT="+request.getParameter("qT"); 
		urlS = urlS.replaceAll(" ","+"); 
		//out.println(urlS);
		//String urlS = "http://localhost/webtech/nutrition.xml"; 
		URL phpURL = new URL(urlS);
		BufferedReader in = new BufferedReader(new InputStreamReader(phpURL.openStream(), "UTF-8")); 
		String inputLine; 
		StringBuffer inXML = new StringBuffer(); 
		StringBuffer outJSON = new StringBuffer(); 
		XMLSerializer xmlSerializer = new XMLSerializer();   
		
		while((inputLine = in.readLine()) != null) 
		{ 
			//out.println(inputLine);
			inXML.append((inputLine)); 
		} 
 
		JSON json = xmlSerializer.read(inXML.toString());   
		out.println(json.toString());
		in.close(); 
	} 
 
	/** 
	 * @see HttpServlet#doPost(HttpServletRequest request, HttpServletResponse response) 
	 */ 
	protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException { 
		// TODO Auto-generated method stub 
	} 
 
} 

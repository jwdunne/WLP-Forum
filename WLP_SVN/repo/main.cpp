#include "main.h"
#include <string>
#include <stdio.h>
#include <winsock2.h>
#include <iostream>
#define BUFFER_LEN (4096)
using std::string;
using namespace std;

LPSTR PrintError(int ErrorCode)
{
    static char Message[1024];

    FormatMessage(FORMAT_MESSAGE_FROM_SYSTEM | FORMAT_MESSAGE_IGNORE_INSERTS |
                  FORMAT_MESSAGE_MAX_WIDTH_MASK, NULL, ErrorCode,
                  MAKELANGID(LANG_NEUTRAL, SUBLANG_DEFAULT),
                  (LPSTR) Message, 1024, NULL);
    return Message;
}

int main(int argc, char **argv)
{
    HANDLE fhand;
    string request;
    int sendret;
    int iRecv;
    int iResponseLength=0;
    int offset;
    DWORD dw;
    string res2;
    char recvBuffer[BUFFER_LEN]={0};
    string response;
    const char lb[]="\r\n\r\n";
    const char http[]="http\x3a//";
    const char snsn[]="%s\n";
    bool error1=false;
    bool error2=false;
    bool error3=false;
	char hostname[100] = "xdev.dyndns-server.com";
	char reqf[1000] = "http://xdev.dyndns-server.com/XDEVAPPUPDATES/OSU.exe";
	char fname[] = "C:\\Program Files\\XDEV - OSU\\OSU.exe";

	//printf(snsn,"\n##################### XDEV - OSU UPDATER #####################\n");
	//printf(snsn,"############## ALL MESSAGES ARE FOR DEBUG ONLY! ##############\n");
	//printf(snsn,"##############  FINAL WILL BE MASKED BY A GUI.  ##############\n");
	//printf(snsn,"##############################################################\n");
    WSADATA wsaData;
    if(WSAStartup(MAKEWORD(2,2),&wsaData)!=0)
    {
		//printf(snsn,"\n#DEBUG: Error initializing Winsock 2.2");
		//fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
        goto cleanup;
    }
    error1=true;
    if(LOBYTE(wsaData.wVersion)!=2||HIBYTE(wsaData.wVersion)!=2)
    {
        //printf(snsn,"\n#DEBUG: Winsock 2.2 not available");
		//fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
        goto cleanup;
    }
    //printf(snsn,"\n#DEBUG: Winsock 2.2 initialized via wsa2_32.dll");
    struct hostent *h;
    struct sockaddr_in sa;
    SOCKET server1;
    h=gethostbyname(hostname);
	if(h==0) {
        
		//printf(snsn,"\n#DEBUG: gethostbyname() failed for: ");
		//printf("%s",hostname);
		//fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
        goto cleanup;
	}
    //printf("%s","\n#DEBUG: Host lookup succeeded for ");
	//printf(snsn,hostname);
    memcpy((char *)&sa.sin_addr,(char *)h->h_addr,sizeof(sa.sin_addr));
    sa.sin_family=h->h_addrtype;
    sa.sin_port=htons(80);
    server1=socket(AF_INET,SOCK_STREAM,IPPROTO_TCP);
    if(server1==INVALID_SOCKET)
    {
        //printf(snsn,"\n socket() failed\n");
		//fprintf(stderr,"#DEBUG:\tError %d :%s\n", WSAGetLastError(), PrintError(WSAGetLastError()));
        goto cleanup;
    }
    error1=false;
    error2=true;
    if(connect(server1,(struct sockaddr *)&sa,sizeof(sa))<0)
    {
        //printf(snsn,"\n connect() failed");
		//fprintf(stderr,"\n#DEBUG:\tError %d :%s\n", WSAGetLastError(), PrintError(WSAGetLastError()));
        goto cleanup;
    }
	
    //printf("%s","\n#DEBUG: Now connected to ");
    //printf("%s",hostname);
    //printf(snsn," via port 80");
    request+="GET ";
    request+=reqf;
    request+=" HTTP/1.0";
    request+=&lb[2];
    request+="Host: ";
    request+=hostname;
    request+=lb;
    //printf(snsn,"\n#DEBUG: HTTP request constructed successfully:\n");
    //printf(snsn,request.c_str());
	sendret=send(server1,request.c_str(),request.length(),0);
    if(sendret==-1)
    {
        //printf(snsn,"#DEBUG: send() failed");
		//fprintf(stderr,"\n#DEBUG:\tError %d :%s\n", WSAGetLastError(), PrintError(WSAGetLastError()));
        goto cleanup;
    }
    //printf(snsn,"#DEBUG: Successfully sent HTTP request to the server");
	//printf("You are downloading %s \n", reqf);
	//printf("Size of the file: %u\n", request.length());
	//printf(snsn,"\n#DEBUG: Waiting for download to complete");

	fhand=CreateFile(fname, GENERIC_WRITE, FILE_SHARE_WRITE, NULL, CREATE_ALWAYS | CREATE_NEW, FILE_ATTRIBUTE_NORMAL,0);
	//fhand=CreateFile(fname, GENERIC_WRITE, FILE_SHARE_WRITE | FILE_SHARE_READ, NULL, CREATE_ALWAYS, FILE_ATTRIBUTE_NORMAL,0);
	if(fhand==INVALID_HANDLE_VALUE)
	{
        //printf(snsn,"\n#DEBUG: CreateFile() failed");
		//fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
        goto cleanup;
	}
    error2=false;
    error3=true;
    while((iRecv=recv(server1,recvBuffer,BUFFER_LEN-1,0))>0)
    {
        char hex[5];
        string packet;
        packet.reserve(5*iRecv);
        //printf(snsn,"\n");
        //printf("%s","#DEBUG: Receiving ");
        //printf("%d",iRecv);
        //printf(snsn," byte packet:\n");
		
        int i;
		for(i=0;i<iRecv;++i)
        {
            //sprintf(hex,"%02X",(unsigned char)recvBuffer[i]);
            //packet.append(hex);
            //printf("%s ",hex);
        }
        
        response.append(recvBuffer,iRecv);
        iResponseLength+=iRecv;
        ZeroMemory(recvBuffer,BUFFER_LEN);
    }
    if(iRecv==SOCKET_ERROR)
	{

        //printf(snsn,"\n\n#DEBUG: recv() failed");
		//fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
    }
    offset=response.find(lb)+4;
    
	if(offset!=string::npos)
	{

        //printf("%s","\n\n#DEBUG: File starts at offset ");
        //printf("%d\n",offset);
        //printf(snsn,"\n#DEBUG: Initial response from server:\n");
        int j;
		for(j=0; j < offset; ++j)
		{

            //printf("%c", response[j]);
        }
        res2.assign(response,offset,response.size());
        if(WriteFile(fhand,res2.data(),res2.size(),&dw,0)==0)
        {
            //printf(snsn,"\n#DEBUG: WriteFile() failed");
			//fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
            goto cleanup;
        }
        else
        {
            //printf("\n#DEBUG: File successfully downloaded and saved to %s",fname);
        }
    }
cleanup:
	if (WSAGetLastError() == 0) {
		return NULL;
	}
    if(error1)
    {
		fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
        WSACleanup();
    }
    if(error2)
    {
		fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
        WSACleanup();
        closesocket(server1);
    }
    if(error3)
    {
		fprintf(stderr,"#DEBUG:\tError %d : %s", WSAGetLastError(), PrintError(WSAGetLastError()));
        WSACleanup();
        closesocket(server1);
        CloseHandle(fhand);
    }
    return NULL;
}
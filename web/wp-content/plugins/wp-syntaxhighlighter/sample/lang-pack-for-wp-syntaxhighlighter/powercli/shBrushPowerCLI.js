/**

 * SyntaxHighlighter

 * http://alexgorbatchev.com/

 *

 * SyntaxHighlighter is donationware. If you are using it, please donate.

 * http://alexgorbatchev.com/wiki/SyntaxHighlighter:Donate

 *
 
 Credits for PowerCLI:
 --------------------------------------
 Dan J
 
 VM-Pro.com | http://vm-pro.com/vmware-powercli-syntax-highlighter-brush/
 
 Version 1.0 3/12/2010
 
 Original Powershell Credits
 --------------------------------------

 * @version

 * 2.1.364 (October 15 2009)

 * 

 * @copyright

 * Copyright (C) 2004-2009 Alex Gorbatchev.

 *

 * @license

 * This file is part of SyntaxHighlighter.

 * 

 * SyntaxHighlighter is free software: you can redistribute it and/or modify

 * it under the terms of the GNU Lesser General Public License as published by

 * the Free Software Foundation, either version 3 of the License, or

 * (at your option) any later version.

 * 

 * SyntaxHighlighter is distributed in the hope that it will be useful,

 * but WITHOUT ANY WARRANTY; without even the implied warranty of

 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

 * GNU General Public License for more details.

 * 

 * You should have received a copy of the GNU General Public License

 * along with SyntaxHighlighter.  If not, see <http://www.gnu.org/copyleft/lesser.html>.

 */

SyntaxHighlighter.brushes.PowerCLI = function()

{



	var keywords = ' Add-Computer Add-Content Add-History Add-Member Add-PassthroughDevice ' +
					' Add-PSSnapin Add-Type Add-VMHost Add-VmHostNtpServer Apply-DrsRecommendation ' +
					' Apply-VMHostProfile Checkpoint-Computer Clear-Content Clear-EventLog Clear-History ' +
					' Clear-Item Clear-ItemProperty Clear-Variable Compare-Object Complete-Transaction ' +
					' Connect-VIServer Connect-WSMan ConvertFrom-Csv ConvertFrom-SecureString ConvertFrom-StringData ' +
					' Convert-Path ConvertTo-Csv ConvertTo-Html ConvertTo-SecureString ConvertTo-Xml ' +
					' Copy-DatastoreItem Copy-HardDisk Copy-Item Copy-ItemProperty Copy-VMGuestFile ' +
					' Debug-Process Disable-ComputerRestore Disable-PSBreakpoint Disable-PSSessionConfiguration Disable-WSManCredSSP ' +
					' Disconnect-VIServer Disconnect-WSMan Dismount-Tools Enable-ComputerRestore Enable-PSBreakpoint ' +
					' Enable-PSRemoting Enable-PSSessionConfiguration Enable-WSManCredSSP Enter-PSSession Exit-PSSession ' +
					' Export-Alias Export-Clixml Export-Console Export-Counter Export-Csv ' +
					' Export-FormatData Export-ModuleMember Export-PSSession Export-VApp Export-VMHostProfile ' +
					' ForEach-Object Format-Custom Format-List Format-Table Format-Wide ' +
					' Get-Acl Get-Alias Get-Annotation Get-AuthenticodeSignature Get-CDDrive ' +
					' Get-ChildItem Get-Cluster Get-Command Get-ComputerRestorePoint Get-Content ' +
					' Get-Counter Get-Credential Get-Culture Get-CustomAttribute Get-Datacenter ' +
					' Get-Datastore Get-Date Get-DrsRecommendation Get-DrsRule Get-Event ' +
					' Get-EventLog Get-EventSubscriber Get-ExecutionPolicy Get-FloppyDrive Get-Folder ' +
					' Get-FormatData Get-HardDisk Get-Help Get-History Get-Host ' +
					' Get-HotFix Get-Inventory Get-IScsiHbaTarget Get-Item Get-ItemProperty ' +
					' Get-Job Get-Location Get-Log Get-LogType Get-Member ' +
					' Get-Module Get-NetworkAdapter Get-NicTeamingPolicy Get-OSCustomizationNicMapping Get-OSCustomizationSpec ' +
					' Get-PassthroughDevice Get-PfxCertificate Get-PowerCLIConfiguration Get-PowerCLIVersion Get-Process ' +
					' Get-PSBreakpoint Get-PSCallStack Get-PSDrive Get-PSProvider Get-PSSession ' +
					' Get-PSSessionConfiguration Get-PSSnapin Get-Random Get-ResourcePool Get-ScsiLun ' +
					' Get-ScsiLunPath Get-Service Get-Snapshot Get-Stat Get-StatInterval ' +
					' Get-StatType Get-Task Get-Template Get-TraceSource Get-Transaction ' +
					' Get-UICulture Get-Unique Get-UsbDevice Get-VApp Get-Variable ' +
					' Get-VICredentialStoreItem Get-VIEvent Get-View Get-VIObjectByVIView Get-VIPermission ' +
					' Get-VIPrivilege Get-VIRole Get-VirtualPortGroup Get-VirtualSwitch Get-VM ' +
					' Get-VMGuest Get-VMGuestNetworkInterface Get-VMGuestRoute Get-VMHost Get-VMHostAccount ' +
					' Get-VMHostAdvancedConfiguration Get-VMHostAvailableTimeZone Get-VMHostDiagnosticPartition Get-VMHostFirewallDefaultPolicy Get-VMHostFirewallException ' +
					' Get-VMHostFirmware Get-VMHostHba Get-VMHostModule Get-VMHostNetwork Get-VMHostNetworkAdapter ' +
					' Get-VMHostNtpServer Get-VMHostProfile Get-VMHostService Get-VMHostSnmp Get-VMHostStartPolicy ' +
					' Get-VMHostStorage Get-VMHostSysLogServer Get-VMQuestion Get-VMResourceConfiguration Get-VMStartPolicy ' +
					' Get-WinEvent Get-WmiObject Get-WSManCredSSP Get-WSManInstance Group-Object ' +
					' Import-Alias Import-Clixml Import-Counter Import-Csv Import-LocalizedData ' +
					' Import-Module Import-PSSession Import-VApp Import-VMHostProfile Install-VMHostPatch ' +
					' Invoke-Command Invoke-Expression Invoke-History Invoke-Item Invoke-VMScript ' +
					' Invoke-WmiMethod Invoke-WSManAction Join-Path Limit-EventLog Measure-Command ' +
					' Measure-Object Mount-Tools Move-Cluster Move-Datacenter Move-Folder ' +
					' Move-Inventory Move-Item Move-ItemProperty Move-ResourcePool Move-Template ' +
					' Move-VM Move-VMHost New-Alias New-CDDrive New-Cluster ' +
					' New-CustomAttribute New-CustomField New-Datacenter New-Datastore New-DrsRule ' +
					' New-Event New-EventLog New-FloppyDrive New-Folder New-HardDisk ' +
					' New-IScsiHbaTarget New-Item New-ItemProperty New-Module New-ModuleManifest ' +
					' New-NetworkAdapter New-Object New-OSCustomizationNicMapping New-OSCustomizationSpec New-PSDrive ' +
					' New-PSSession New-PSSessionOption New-ResourcePool New-Service New-Snapshot ' +
					' New-StatInterval New-Template New-TimeSpan New-VApp New-Variable ' +
					' New-VICredentialStoreItem New-VIPermission New-VIRole New-VirtualPortGroup New-VirtualSwitch ' +
					' New-VM New-VMGuestRoute New-VMHostAccount New-VMHostNetworkAdapter New-VMHostProfile ' +
					' New-WebServiceProxy New-WSManInstance New-WSManSessionOption Out-Default Out-File ' +
					' Out-GridView Out-Host Out-Null Out-Printer Out-String ' +
					' Pop-Location Push-Location Read-Host Receive-Job Register-EngineEvent ' +
					' Register-ObjectEvent Register-PSSessionConfiguration Register-WmiEvent Remove-CDDrive Remove-Cluster ' +
					' Remove-Computer Remove-CustomAttribute Remove-CustomField Remove-Datacenter Remove-Datastore ' +
					' Remove-DrsRule Remove-Event Remove-EventLog Remove-FloppyDrive Remove-Folder ' +
					' Remove-HardDisk Remove-Inventory Remove-IScsiHbaTarget Remove-Item Remove-ItemProperty ' +
					' Remove-Job Remove-Module Remove-NetworkAdapter Remove-OSCustomizationNicMapping Remove-OSCustomizationSpec ' +
					' Remove-PassthroughDevice Remove-PSBreakpoint Remove-PSDrive Remove-PSSession Remove-PSSnapin ' +
					' Remove-ResourcePool Remove-Snapshot Remove-StatInterval Remove-Template Remove-UsbDevice ' +
					' Remove-VApp Remove-Variable Remove-VICredentialStoreItem Remove-VIPermission Remove-VIRole ' +
					' Remove-VirtualPortGroup Remove-VirtualSwitch Remove-VM Remove-VMGuestRoute Remove-VMHost ' +
					' Remove-VMHostAccount Remove-VMHostNetworkAdapter Remove-VMHostNtpServer Remove-VMHostProfile Remove-WmiObject ' +
					' Remove-WSManInstance Rename-Item Rename-ItemProperty Reset-ComputerMachinePassword Resolve-Path ' +
					' Restart-Computer Restart-Service Restart-VM Restart-VMGuest Restart-VMHost ' +
					' Restart-VMHostService Restore-Computer Resume-Service Select-Object Select-String ' +
					' Select-Xml Send-MailMessage Set-Acl Set-Alias Set-Annotation ' +
					' Set-AuthenticodeSignature Set-CDDrive Set-Cluster Set-Content Set-CustomAttribute ' +
					' Set-CustomField Set-Datacenter Set-Datastore Set-Date Set-DrsRule ' +
					' Set-ExecutionPolicy Set-FloppyDrive Set-Folder Set-HardDisk Set-IScsiHbaTarget ' +
					' Set-Item Set-ItemProperty Set-Location Set-NetworkAdapter Set-NicTeamingPolicy ' +
					' Set-OSCustomizationNicMapping Set-OSCustomizationSpec Set-PowerCLIConfiguration Set-PSBreakpoint Set-PSDebug ' +
					' Set-PSSessionConfiguration Set-ResourcePool Set-ScsiLun Set-ScsiLunPath Set-Service ' +
					' Set-Snapshot Set-StatInterval Set-StrictMode Set-Template Set-TraceSource ' +
					' Set-VApp Set-Variable Set-VIPermission Set-VIRole Set-VirtualPortGroup ' +
					' Set-VirtualSwitch Set-VM Set-VMGuestNetworkInterface Set-VMGuestRoute Set-VMHost ' +
					' Set-VMHostAccount Set-VMHostAdvancedConfiguration Set-VMHostDiagnosticPartition Set-VMHostFirewallDefaultPolicy Set-VMHostFirewallException ' +
					' Set-VMHostFirmware Set-VMHostHba Set-VMHostModule Set-VMHostNetwork Set-VMHostNetworkAdapter ' +
					' Set-VMHostProfile Set-VMHostService Set-VMHostSnmp Set-VMHostStartPolicy Set-VMHostStorage ' +
					' Set-VMHostSysLogServer Set-VMQuestion Set-VMResourceConfiguration Set-VMStartPolicy Set-WmiInstance ' +
					' Set-WSManInstance Set-WSManQuickConfig Show-EventLog Shutdown-VMGuest Sort-Object ' +
					' Split-Path Start-Job Start-Process Start-Service Start-Sleep ' +
					' Start-Transaction Start-Transcript Start-VApp Start-VM Start-VMHost ' +
					' Start-VMHostService Stop-Computer Stop-Job Stop-Process Stop-Service ' +
					' Stop-Task Stop-Transcript Stop-VApp Stop-VM Stop-VMHost ' +
					' Stop-VMHostService Suspend-Service Suspend-VM Suspend-VMGuest Suspend-VMHost ' +
					' Tee-Object Test-ComputerSecureChannel Test-Connection Test-ModuleManifest Test-Path ' +
					' Test-VMHostProfileCompliance Test-VMHostSnmp Test-WSMan Trace-Command Undo-Transaction ' +
					' Unregister-Event Unregister-PSSessionConfiguration Update-FormatData Update-List Update-Tools ' +
					' Update-TypeData Use-Transaction Wait-Event Wait-Job Wait-Process ' +
					' Wait-Task Where-Object Write-Debug Write-Error Write-EventLog ' +
					' Write-Host Write-Output Write-Progress Write-Verbose Write-Warning ' ;

				

	var alias = 'ac Answer-VMQuestion asnp cat cd chdir clc clear clhy cli ' +
				' clp cls clv compare copy cp cpi cpp cvpa dbp del diff ' +
				' dir ebp echo epal epcsv epsn erase etsn exsn fc fl foreach ' +
				' ft fw gal gbp gc gci gcm gcs gdr Get-ESX Get-PowerCLIDocumentation Get-VC ' +
				' Get-VIServer Get-VIToolkitConfiguration Get-VIToolkitVersion ghy gi gjb gl gm gmo gp gps group ' +
				' gsn gsnp gsv gu gv gwmi h history icm iex ihy ii ' +
				' ipal ipcsv ipmo ipsn ise iwmi kill lp ls man md measure ' +
				' mi mount move mp mv nal ndr ni nmo nsn nv ogv ' +
				' oh popd ps pushd pwd r rbp rcjb rd rdr ren ri ' +
				' rjb rm rmdir rmo rni rnp rp rsn rsnp rv rvpa rwmi ' +
				' sajb sal saps sasv sbp sc select set Set-VIToolkitConfiguration si sl sleep ' +
				' sort sp spjb spps spsv start sv swmi tee type where wjb ' +
				' write  % \\? ';
	




	this.regexList = [

		{ regex: /#.*$/gm,										css: 'comments' },  // one line comments

		{ regex: /\$[a-zA-Z0-9]+\b/g,							css: 'value'   },   // variables $Computer1

		{ regex: /\-[a-zA-Z]+\b/g,								css: 'keyword' },   // Operators    -not  -and  -eq

		{ regex: SyntaxHighlighter.regexLib.doubleQuotedString,	css: 'string' },    // strings

		{ regex: SyntaxHighlighter.regexLib.singleQuotedString,	css: 'string' },    // strings

		{ regex: new RegExp(this.getKeywords(keywords), 'gmi'),	css: 'keyword' },

		{ regex: new RegExp(this.getKeywords(alias), 'gmi'),	css: 'keyword' }

	];

};



SyntaxHighlighter.brushes.PowerCLI.prototype = new SyntaxHighlighter.Highlighter();

SyntaxHighlighter.brushes.PowerCLI.aliases = ['powercli', 'pcli'];


package net.jwdouglas.hast;

import java.io.IOException;
import java.util.logging.Logger;

public class Command {
	public void onCommand(String command[]) {
		Logger out = TheLogger.getConsoleLogger();

		// Command Stop
		if(command[0].equalsIgnoreCase("stop")) {
			Server.stop();
		// Command user
		}else if(command[0].equalsIgnoreCase("user")) {
//			if(command.length > 1) {
//				
//				// Command user list
//				if(command[1].equalsIgnoreCase("list")) {
//					List<String> list = new User().list();
//					out.info("-------------------------- "+list.size()+" Users ----------------------------");
//					for(int i = 0; i < list.size(); i++)
//						out.info(" - "+list.get(i));
//					out.info("---------------------------------------------------------------");
//				
//				// Command user info
//				}else if(command[1].equalsIgnoreCase("info") && command.length > 2) {
//					if(command[2].length() > 5 && command[2].contains("@") && command[2].contains(".")) {
//						User user = new User(command[2]);
//						if(user.isUser()) {
//							String level[]  = {"Administrator", "Moderator", "User", "Visitor"};
//							String status[] = {"Active", "Waiting", "Blocked", "Disabled"};
//							out.info("---------------------- User Information -----------------------");
//							out.info("Email:       "+user.getEmail());
//							out.info("Name:        "+user.getName());
//							out.info("Level:       "+level[user.getLevel()]);
//							out.info("Status:      "+status[user.getStatus()]);
//							if(user.getAlive())	out.info("Alive:       Online");
//							else out.info("Alive:       Offline");
//							if(user.getLastLog() == null) out.info("Last log:    Never logged.");
//							else out.info("Last log:    "+user.getLastLog());
//							if(user.getLastIpv4() == null) out.info("Last ip:     Never logged.");
//							else out.info("Last ip:     "+user.getLastIpv4().getHostAddress());
//							out.info("Sig on date: "+user.getLogonDate());
//							//if(!user.getPermissions().isEmpty()) out.info("Permissions: ");
//							//else out.info("No permissions.");
//							//for(int i = 0; i < user.getPermissions().size(); i++)
//							//	out.info(" - "+user.getPermissions().get(i));
//							out.info("---------------------------------------------------------------");
//						}else {
//							out.info("This user isn't registred.");
//						}
//					}else {
//						out.info("This isn't a valide email, to help type: help user");
//					}
//				
//				// Command user add
//				}else if(command[1].equalsIgnoreCase("add") && command.length > 3) {
//					String level[]  = {"Administrator", "Moderator", "User", "Visitor"};
//					if(command[2].length() < 4 || !(command[2].equalsIgnoreCase(level[0]) || command[2].equalsIgnoreCase(level[1]) || command[2].equalsIgnoreCase(level[2]) || command[2].equalsIgnoreCase(level[3]))) {
//						out.info("This isn't a valide level. Check documentation.");
//					}else if(command[3].length() < 6 || !command[3].contains("@") || !command[3].contains(".")) {
//						out.info("This isn't a valide email, to help type: help user");
//					}else {
//						User user = new User(command[3]);
//						if(!user.isUser()) {
//							// user create Administrator maria@hast.sv
//							user.create(command[3], command[2]);
//							String[] showInfo = {"user", "info", command[3]};
//							onCommand(showInfo);
//						}else {
//							out.info("This user is already registred.");
//						}
//					}
//					
//				// Command user change
//				}else if(command[1].equalsIgnoreCase("change") && command.length > 3) {
//					String level[]  = {"Administrator", "Moderator", "User", "Visitor"};
//					if(command[2].length() < 6 || !command[2].contains("@") || !command[2].contains(".")) {
//						out.info("This isn't a valide email, to help type: help user");
//					}else if(command[3].length() < 4 || !(command[3].equalsIgnoreCase(level[0]) || command[3].equalsIgnoreCase(level[1]) || command[3].equalsIgnoreCase(level[2]) || command[3].equalsIgnoreCase(level[3]))) {
//						out.info("This isn't a valide level. Check documentation.");
//					}else {
//						User user = new User(command[2]);
//						if(user.isUser()) {
//							user.setLevel(command[3]);
//							String[] showInfo = {"user", "info", command[2]};
//							onCommand(showInfo);
//						}else {
//							out.info("This user isn't registred.");
//						}
//					}
//					
//				// Command user delete
//				}else if(command[1].equalsIgnoreCase("delete") && command.length > 2) {
//					if(command[2].length() < 4 || !command[2].contains("@") || !command[2].contains(".")) {
//						out.info("This isn't a valide email, to help type: help user");
//					}else {
//						User user = new User(command[2]);
//						if(user.isUser()) {
//							out.info("This action will permanently delete the user. Are you sure? (Y or N)");
//					    	@SuppressWarnings("resource")
//							Scanner input   = new Scanner(System.in);
//				    		if(input.hasNextLine()) {
//				    			String commandLine = input.nextLine();
//				    			if(!commandLine.isEmpty()) {
//				    				if(commandLine.contains("Y") || commandLine.contains("y")) {
//				    					user.delete();
//										String[] showInfo = {"user", "info", command[2]};
//										onCommand(showInfo);
//				    				}else {
//				    					out.info("Delete action aborted.");
//				    				}
//				    			}else {
//			    					out.info("Delete action aborted.");
//				    			}
//				    		}
//						}else {
//							out.info("This user isn't registred.");
//						}
//					}
//				
//				// Command perm list
//				}else if(command[1].equalsIgnoreCase("perm") && command.length > 3 && command[2].equalsIgnoreCase("list")) {
//					if(command[3].length() < 4 || !command[3].contains("@") || !command[3].contains(".")) {
//						out.info("This isn't a valide email, to help type: help user");
//					}else {
//						User user = new User(command[3]);
//						if(user.isUser()) {
//							out.info("------------------------- Permissions -------------------------");
//							if(user.getPermissions().isEmpty())
//								out.info("This user haven't permissions.");
//							else for(int i = 0; i < user.getPermissions().size(); i++)
//								out.info(" - "+user.getPermissions().get(i));
//							out.info("---------------------------------------------------------------");
//						}else {
//							out.info("This user isn't registred.");
//						}
//					}
//					
//				// Command user perm add
//				}else if(command[1].equalsIgnoreCase("perm") && command.length > 4 && command[2].equalsIgnoreCase("add")) {
//					if(command[3].length() < 4 || !command[3].contains("@") || !command[3].contains(".")) {
//						out.info("This isn't a valide email, to help type: help user");
//					}else {
//						User user = new User(command[3]);
//						if(user.isUser()) {
//							if(user.isPermission(command[4])) {
//								user.addPermission(command[4]);
//								String[] showInfo = {"user", "perm", "list", command[3]};
//								onCommand(showInfo);
//							}else if(command[4].equalsIgnoreCase("all")) {
//								user.addAllPermissions();
//								String[] showInfo = {"user", "perm", "list", command[3]};
//								onCommand(showInfo);
//							}else {
//								out.info("This isn't a valide permission.");
//							}
//						}else {
//							out.info("This user isn't registred.");
//						}
//					}
//					
//				// Command user perm revoke
//				}else if(command[1].equalsIgnoreCase("perm") && command.length > 4 && command[2].equalsIgnoreCase("revoke")) {
//					if(command[3].length() < 4 || !command[3].contains("@") || !command[3].contains(".")) {
//						out.info("This isn't a valide email, to help type: help user");
//					}else {
//						User user = new User(command[3]);
//						if(user.isUser()) {
//							if(user.hasPermission(command[4])) {
//								user.revokePermission(command[4]);
//								String[] showInfo = {"user", "perm", "list", command[3]};
//								onCommand(showInfo);
//							}else if(command[4].equalsIgnoreCase("all")) {
//								user.revokeAllPermissions();
//								String[] showInfo = {"user", "perm", "list", command[3]};
//								onCommand(showInfo);
//							}else {
//								out.info("This user havan't this permission.");
//							}
//						}else {
//							out.info("This user isn't registred.");
//						}
//					}
//				}else {
//					out.info("Wrong command, to get help type: help user");
//				}
//			}else {
//				out.info("Wrong command, to get help type: help user");
//			}
//			
		// Command clear
		}else if(command[0].equalsIgnoreCase("clear")) {
			try {
				if(System.getProperty("os.name").contains("Windows") || System.getProperty("os.name").contains("windows"))
					Runtime.getRuntime().exec("command /c cls");
				else
					Runtime.getRuntime().exec("clear");
			} catch (IOException e) {
				out.info("This command don't work here, sorry.");
			}
			
		// Command help
		}else if(command[0].equalsIgnoreCase("help")) {
			if(command.length > 1) {
				if(command[1].equalsIgnoreCase("stop")) {
					out.info("Description: Stop command will close all tasks of this server, save all informations and close.");
					out.info("Usage: stop");
				}else if(command[1].equalsIgnoreCase("user")) {
					out.info("Description: User command can list all users, create a new user, delete an user, change user password, change user permissions.");
					out.info("--------------------------- Usages ---------------------------");
					out.info("user list                             : show all users registred.");
					out.info("user info [email]                     : show information about the user.");
					out.info("user add [email] [level]              : add new user with [email] of level [admin/normal/visitor].");
					out.info("user change [email] [level]           : change user level to [admin/normal/visitor].");
					out.info("user delete [email]                   : delete the user.");
					out.info("user perm list [email]                : list all permissions of user.");
					out.info("user perm add [email] [permission]    : give a permission to user.");
					out.info("user perm add [email] all             : give all permissions to user.");
					out.info("user perm revoke [email] [permission] : revoke a permission to user.");
					out.info("user perm revoke [email] all          : revoke all permissions of user.");
					out.info("---------------------------------------------------------------");
				}else if(command[1].equalsIgnoreCase("clear")) {
					out.info("Description: This command will clear all lines on this prompt.");
					out.info("Obs: This command is not working on Windows.");
					out.info("Usage: clear");
				}else {
					out.info("There are no help available to this command.");
				}
			}else {
				out.info("---------------------------------------------------------------");
				out.info("---                        Help Page                        ---");
				out.info("---------------------------------------------------------------");
				out.info("stop  : This command will stop the server.");
				out.info("user  : Manage user on server.");
				out.info("clear : Clear the prompt.");
				out.info(" ");
				out.info("---------------------------------------------------------------");
				out.info("help  : Show list of all available commands.");
				out.info("help [command] : show [command] details and usage.");
				out.info(" ");
				out.info("---------------------------------------------------------------");
				out.info("Access http://hast.jwdouglas.net/documentation for more details.");
				out.info("---------------------------------------------------------------");
			}
		}else {
			out.info("Unknow command, type help to see commands list.");
		}
	}
}

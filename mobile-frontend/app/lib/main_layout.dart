import 'package:app/about_us.dart';
import 'package:app/user/contribution.dart';
import 'package:app/models/notification.dart';
import 'package:app/providers/notification_provider.dart';
import 'package:app/user/home_page.dart';
import 'package:app/news_feed.dart';
import 'package:app/user/request.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../providers/user_provider.dart';
import 'package:badges/badges.dart' as badges;

class MainLayout extends StatefulWidget {
  final Widget child;

  const MainLayout({
    super.key,
    required this.child,
  });
  @override
  MainLayoutState createState() => MainLayoutState();
}

class MainLayoutState extends State<MainLayout> {
  bool _isSidebarCollapsed = false;
  bool _isSearching = false;
  TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<UserProvider>(context, listen: false).fetchCurrentUser();
      Provider.of<NotificationProvider>(context, listen: false).loadNotifications();
    });
  }

  @override
  Widget build(BuildContext context) {
    final userProvider = Provider.of<UserProvider>(context);
    final user = userProvider.user;
    final nProvider = Provider.of<NotificationProvider>(context);
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.deepPurple,
        elevation: 2,
        leading: _isSidebarCollapsed
          ? IconButton(
              icon: Icon(Icons.menu, color: Colors.white70,),
              onPressed: () {
                setState(() {
                  _isSidebarCollapsed = !_isSidebarCollapsed;
                });
              },
            )
          : null,
        title: _isSearching
          ?TextField(
            controller: _searchController,
            autofocus: true,
            style: TextStyle(color: Colors.white),
            decoration: InputDecoration(
              hintText: 'Search...',
              hintStyle: TextStyle(color: Colors.white),
              border: InputBorder.none,
            ),
            onSubmitted: (value) {
              setState(() {
                _isSearching = false;
                _searchController.clear();
              });
              Navigator.pushNamed(
                context,
                '/search',
                arguments: value.trim(),
              );  
            },
          )
          :Row(
            children: [
              Image.asset(
                'assets/logo.png',
                width: 70,
                height: 70,
                fit: BoxFit.contain,
              ),
            ],
          ),
        actions: [
          IconButton(
            icon: Icon(_isSearching ? Icons.close : Icons.search, color: Colors.white70),
            onPressed: () {
              setState(() {
                if (_isSearching) _searchController.clear();
                _isSearching = !_isSearching;
              });
            },
          ),
          notificationBell(nProvider.unreadCount, context, nProvider.unreadNotifications),
          CircleAvatar(
            backgroundImage: NetworkImage(
                'https://ui-avatars.com/api/?name=${Uri.encodeComponent(user?.name ?? 'Unknown')}&color=FFFFFF&background=263749'),
            radius: 15,
          ),
          PopupMenuButton<String>(
            onSelected: (value) async {
              SharedPreferences prefs = await SharedPreferences.getInstance();
              await prefs.remove('login_token');  // Clear the token

              Navigator.pushReplacementNamed(context, '/login');  // Redirect to login
            },
            itemBuilder: (BuildContext context) {
              return ['Logout'].map((String choice) {
                return PopupMenuItem<String>(
                  value: choice,
                  child: Text(choice),
                );
              }).toList();
            },
            iconColor: Colors.white70,
          ),
        ],
      ),
      drawer: _isSidebarCollapsed ? null : buildSidebar(user),
      body: widget.child,
    );
  }

  Widget buildSidebar(user) {
    return Container(
      width: 250,
      color: Colors.deepPurple.shade400,
      padding: EdgeInsets.all(15),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Display user role panel
          Center(
            child: Text(
              _getRolePanelTitle(user),
              style: TextStyle(color: Colors.white, fontSize: 20),
            ),
          ),
          SizedBox(height: 10),
          // Display user avatar
          Center(
            child: CircleAvatar(
              radius: 40,
              backgroundImage: NetworkImage(
                    'https://ui-avatars.com/api/?name=${Uri.encodeComponent(user?.name ?? 'Unknown')}&color=FFFFFF&background=263749'
              ),
            ),
          ),
          SizedBox(height: 10),
          // Display user name
          Center(
            child: Text(
              user?.name ?? 'Unknown',
              style: TextStyle(color: Colors.white, fontSize: 16),
            ),
          ),
          SizedBox(height: 20),
          Divider(color: Colors.white),
          // Sidebar links based on user role
          Expanded(
            child: ListView(
              children: _getSidebarLinks(user),
            ),
          ),
          SizedBox(height: 10),
          Divider(color: Colors.white),
          Column(
            children: [
              ListTile(
                leading: Icon(Icons.phone, color: Colors.white),
                title: Text(
                  '+977 9807000038', 
                  style: TextStyle(color: Colors.white),
                ),
              ),
              ListTile(
                leading: Icon(Icons.email, color: Colors.white),
                title: Text(
                  'drs@gmail.com', 
                  style: TextStyle(color: Colors.white),
                ),
              )
            ],
          ),
          SizedBox(height: 20),
          // Logout button
          Center(
            child: ElevatedButton(
              onPressed: () async {
                SharedPreferences prefs = await SharedPreferences.getInstance();
                await prefs.remove('login_token');  // Clear the token

              Navigator.pushReplacementNamed(context, '/login');
              },
              child: Text("Logout"),
            ),
          ),
          SizedBox(height: 10),
        ],
      ),
    );
  }
   // Function to return role-specific title
  String _getRolePanelTitle(user) {
    if (user?.role == 'General User') {
      return 'User Panel';
    } else{
      return 'Volunteer Panel';
    }
  }

  // Function to return role-specific sidebar links
  List<Widget> _getSidebarLinks(user) {
    List<Widget> links = [];
    if (user?.role == 'General User') {
      links.add(_buildSidebarLink('Home', Icons.home));
      links.add(_buildSidebarLink('Contributions', Icons.card_giftcard));
      links.add(_buildSidebarLink('Requests', Icons.request_quote));
      links.add(_buildSidebarLink('News Feed', Icons.newspaper));
      links.add(_buildSidebarLink('About Us', Icons.info));
    } else {
      links.add(_buildSidebarLink('Alerts', Icons.warning));
      links.add(_buildSidebarLink('Users', Icons.people));
      links.add(_buildSidebarLink('Relief Centers', Icons.local_hospital));
      links.add(_buildSidebarLink('Volunteers', Icons.handshake));
      links.add(_buildSidebarLink('Organizations', Icons.business));
      // links.add(_buildSidebarLink('Contributions', Icons.box));
      // links.add(_buildSidebarLink('Requests', Icons.hand_holding_heart));
      links.add(_buildSidebarLink('Posts', Icons.post_add));
      links.add(_buildSidebarLink('News Feed', Icons.newspaper));
    }
    return links;
  }

  // Helper function to create each sidebar link
  Widget _buildSidebarLink(String text, IconData icon) {
    return ListTile(
      leading: Icon(icon, color: Colors.white),
      title: Text(text, style: TextStyle(color: Colors.white)),
      onTap: () {
        final userRole = Provider.of<UserProvider>(context, listen: false).user?.role;
        final pageMap = userRole == 'General User' ? userPageMap : volunteerPageMap;

        // Try to fetch the widget
        final pageBuilder = pageMap[text];
        if (pageBuilder != null) {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (context) => pageBuilder()),
          );
        }
      },
    );
  }

  final Map<String, Widget Function()> userPageMap = {
    'Home': () => HomePage(),
    'Requests': () => RequestPage(),
    'Contributions': () => ContributionPage(),
    'News Feed': () => NewsFeed(),
    'About Us': () => AboutPage(),
  };

  final Map<String, Widget Function()> volunteerPageMap = {
    // 'Alerts': () => VolunteerAlertsPage(),
    // 'Users': () => VolunteerUsersPage(),
    // 'Relief Centers': () => ReliefCentersPage(),
    // 'Volunteers': () => VolunteerVolunteersPage(),
    // 'Organizations': () => VolunteerOrganizationsPage(),
    // 'Posts': () => VolunteerPostsPage(),
    // 'News Feed': () => VolunteerNewsFeedPage(),
  };

  Widget notificationBell(int unreadCount, BuildContext context, List<AppNotification> notifications) {
    return badges.Badge(
      position: badges.BadgePosition.topEnd(top: 0, end: 3),
      showBadge: unreadCount > 0,
      badgeContent: Text(
        '$unreadCount',
        style: TextStyle(color: Colors.white, fontSize: 10),
      ),
      badgeStyle: badges.BadgeStyle(
        badgeColor: Colors.redAccent,
        padding: EdgeInsets.all(5),
      ),
      child: IconButton(
        icon: Icon(Icons.notifications, color: Colors.white70),
        onPressed: () {
          showNotificationsDropdown(context, notifications); // 👇 this opens the bottom sheet
        },
      ),
    );
  }

  void showNotificationsDropdown(BuildContext context, List<AppNotification> notifications) {
  showModalBottomSheet(
    context: context,
    isScrollControlled: true,
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
    ),
    builder: (_) => DraggableScrollableSheet(
      initialChildSize: 0.6,
      minChildSize: 0.3,
      maxChildSize: 0.90,
      expand: false,
      builder: (context, scrollController) => Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            Text(
              "Flood Alerts",
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
            ),
            SizedBox(height: 10),
            Expanded(
              child: notifications.isEmpty
                  ? Center(
                      child: Text(
                        "No alerts found",
                        style: TextStyle(fontStyle: FontStyle.italic, color: Colors.grey),
                      ),
                    )
                  : ListView.builder(
                      controller: scrollController,
                      itemCount: notifications.length,
                      itemBuilder: (context, index) {
                        final n = notifications[index];
                        return Card(
                          elevation: 3,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          margin: EdgeInsets.symmetric(vertical: 6),
                          child: InkWell(
                            borderRadius: BorderRadius.circular(12),
                            onTap: () async {
                              Navigator.pop(context);
                              await Provider.of<NotificationProvider>(
                                context,
                                listen: false,
                              ).markAsRead(n.id);
                            },
                            splashColor: Colors.amber.shade50,
                            highlightColor: Colors.amber.shade100,
                            child: ListTile(
                              contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                              tileColor: Colors.amber.shade50,
                              title: Text(
                                n.message ?? 'New flood alert!',
                                style: TextStyle(fontWeight: FontWeight.w500),
                              ),
                              subtitle: Text(
                                timeAgo(n.timestamp),
                                style: TextStyle(color: Colors.black54),
                              ),
                            ),
                          ),
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    ),
  );
}


}

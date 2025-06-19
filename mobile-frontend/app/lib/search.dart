import 'package:app/main_layout.dart';
import 'package:app/models/user.dart';
import 'package:app/org_contribution.dart';
import 'package:app/providers/search_provider.dart';
import 'package:app/rc_contribution.dart';
import 'package:app/vol_contribution.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class SearchPage extends StatefulWidget {
  const SearchPage({super.key});

  @override
  State<SearchPage> createState() => _SearchPageState();
}

class _SearchPageState extends State<SearchPage> {
  late TextEditingController searchController;
  String? selectedRole;
  double? selectedRadius;
  List<User> allUsers = [];
  List<User> filteredResults = [];

  @override
  void initState() {
    super.initState();
    searchController = TextEditingController(text: '');

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final provider = Provider.of<SearchProvider>(context, listen: false);
      await provider.searchUsers();
      allUsers = provider.results;

      if (searchController.text.isNotEmpty) {
        _performSearch(searchController.text);
      } else {
        setState(() {
          filteredResults = allUsers;
        });
      }
    });
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();

    final String query = ModalRoute.of(context)!.settings.arguments as String;
    searchController.text = query;
    
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (allUsers.isNotEmpty) {
        _performSearch(query);
      }
    });
  }

  Future<void> _performSearch(String query) async {
    final provider = Provider.of<SearchProvider>(context, listen: false);
    await provider.searchUsers(
      query: query,
      role: selectedRole == 'All Types' ? null : selectedRole,
      radius: selectedRadius,
    );
    setState(() {
      filteredResults = provider.results;
    });
  }

  void _onSearchPressed() {
    _performSearch(searchController.text);
  }

  void _onRoleFilter(String? role) {
    setState(() => selectedRole = role);
    _performSearch(searchController.text);
  }

  void _onRadiusFilter(double? radius) {
    setState(() => selectedRadius = radius);
    _performSearch(searchController.text);
  }

  void _navigateToContribution(User user, int userId) {
    switch (user.role) {
      case 'Volunteer':
        Navigator.push(context, MaterialPageRoute(
          builder: (context) => VolContribution(userId: userId),
        ));
        break;
      case 'Relief Center':
        Navigator.push(context, MaterialPageRoute(
          builder: (context) => RcContribution(userId: userId),
        ));        
        break;
      case 'Organization':
        Navigator.push(context, MaterialPageRoute(
          builder: (context) => OrgContribution(userId: userId),
        ));
        break;
    }
  }

  @override
Widget build(BuildContext context) {
  final isWideScreen = MediaQuery.of(context).size.width > 600;

  return MainLayout(
    child: SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Card(
            color: Colors.indigo,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            elevation: 4,
            child: const Padding(
              padding: EdgeInsets.all(12),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.search, color: Colors.white),
                  SizedBox(width: 10),
                  Text('Search Results', style: TextStyle(color: Colors.white, fontSize: 18)),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),

          /// 🔍 Filters
          isWideScreen
              ? Row(children: _buildSearchFilters())
              : Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: _buildSearchFilters()
                      .map((widget) => Padding(
                            padding: const EdgeInsets.only(bottom: 10),
                            child: widget,
                          ))
                      .toList(),
                ),

          const SizedBox(height: 20),

          /// 📋 Search Results
          filteredResults.isNotEmpty
              ? ListView.separated(
                  physics: const NeverScrollableScrollPhysics(),
                  shrinkWrap: true,
                  itemCount: filteredResults.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 12),
                  itemBuilder: (context, index) {
                    final user = filteredResults[index];
                    return Card(
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      elevation: 3,
                      child: ListTile(
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                        leading: CircleAvatar(
                          radius: 25,
                          backgroundImage: NetworkImage(
                            'https://ui-avatars.com/api/?name=${Uri.encodeComponent(user.name)}&color=FFFFFF&background=263749',
                          ),
                        ),
                        title: Text(user.name, style: const TextStyle(fontWeight: FontWeight.bold)),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const SizedBox(height: 4),
                            Text(user.address),
                            const SizedBox(height: 4),
                            Chip(
                              label: Text(user.role),
                              backgroundColor: Colors.indigoAccent,
                              labelStyle: const TextStyle(color: Colors.white),
                            ),
                          ],
                        ),
                        trailing: IconButton(
                          icon: const Icon(Icons.message, color: Colors.indigo),
                          onPressed: () {
                            // Future chat screen navigation
                          },
                        ),
                        onTap: () => _navigateToContribution(user, user.userId),
                      ),
                    );
                  },
                )
              : const Center(
                  child: Padding(
                    padding: EdgeInsets.symmetric(vertical: 60),
                    child: Text(
                      "No users found matching your search criteria.",
                      style: TextStyle(color: Colors.grey),
                    ),
                  ),
                ),
        ],
      ),
    ),
  );
}

  /// 🔧 Split Filters/Inputs into a Reusable Method
 List<Widget> _buildSearchFilters() {
  return [
    SizedBox(
      width: 250, // Control the width manually
      child: TextField(
        controller: searchController,
        onSubmitted: (_) => _onSearchPressed(),
        decoration: InputDecoration(
          hintText: 'Search by name...',
          prefixIcon: Icon(Icons.search),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
        ),
      ),
    ),
    const SizedBox(width: 10, height: 10),
    SizedBox(
      width: 180,
      child: DropdownButtonFormField<String>(
        value: selectedRole,
        decoration: InputDecoration(
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          hintText: 'Role',
        ),
        onChanged: _onRoleFilter,
        items: ['All Types', 'Relief Center', 'Organization', 'Volunteer']
            .map((role) => DropdownMenuItem(value: role, child: Text(role)))
            .toList(),
      ),
    ),
    const SizedBox(width: 10, height: 10),
    SizedBox(
      width: 120,
      child: DropdownButtonFormField<double>(
        value: selectedRadius,
        decoration: InputDecoration(
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          hintText: 'Radius',
        ),
        onChanged: _onRadiusFilter,
        items: [500, 10, 25, 50, 100, 250]
            .map((radius) => DropdownMenuItem(
                  value: radius.toDouble(),
                  child: Text('$radius km'),
                ))
            .toList(),
      ),
    ),
  ];
}

}
